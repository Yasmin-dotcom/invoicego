<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Invoice extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SENT = 'sent';
    public const STATUS_PAID = 'paid';
    public const STATUS_OVERDUE = 'overdue';

    protected $fillable = [
        'user_id',
        'client_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'total',
        'status',
        'paid_at',
        'razorpay_payment_link',
        'razorpay_order_id',
        'reminder_count',
        'last_reminded_at',
        'payment_token',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'last_reminded_at' => 'datetime',
        'total' => 'decimal:2',
        'reminder_count' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $invoice) {
            if (! $invoice->payment_token) {
                do {
                    $token = Str::random(40);
                } while (self::query()->where('payment_token', $token)->exists());

                $invoice->payment_token = $token;
            }
        });
    }

    /**
     * Invoice belongs to a client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Invoice has many items
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Normalize legacy / mixed-case statuses into the supported lifecycle.
     * Backward-compat notes:
     * - legacy "unpaid" behaves like "sent" (eligible for overdue/reminders)
     * - legacy "pending" behaves like "draft"
     */
    public function normalizedStatus(): string
    {
        $raw = strtolower(trim((string) ($this->attributes['status'] ?? '')));

        return match ($raw) {
            self::STATUS_DRAFT => self::STATUS_DRAFT,
            self::STATUS_SENT => self::STATUS_SENT,
            self::STATUS_PAID => self::STATUS_PAID,
            self::STATUS_OVERDUE => self::STATUS_OVERDUE,
            'unpaid' => self::STATUS_SENT,
            'pending', '' => self::STATUS_DRAFT,
            default => self::STATUS_DRAFT,
        };
    }

    /**
     * Helper to "auto-mark" an invoice as overdue based on lifecycle rules:
     * - status is SENT
     * - due_date < today
     *
     * This returns the effective lifecycle status (does not persist).
     */
    public function lifecycleStatus(): string
    {
        $status = $this->normalizedStatus();

        if (
            $status === self::STATUS_SENT
            && $this->due_date
            && $this->due_date->lt(Carbon::today())
        ) {
            return self::STATUS_OVERDUE;
        }

        return $status;
    }

    public function getLifecycleStatusAttribute(): string
    {
        return $this->lifecycleStatus();
    }

    public function isRemindable(): bool
    {
        return in_array($this->lifecycleStatus(), [self::STATUS_SENT, self::STATUS_OVERDUE], true);
    }

    /**
     * Persist the lifecycle overdue rule to the database (idempotent).
     */
    public function markOverdueIfNeeded(): bool
    {
        if ($this->normalizedStatus() !== self::STATUS_SENT) {
            return false;
        }

        if (!$this->due_date || !$this->due_date->lt(Carbon::today())) {
            return false;
        }

        if (strtolower((string) $this->status) === self::STATUS_OVERDUE) {
            return false;
        }

        $this->forceFill(['status' => self::STATUS_OVERDUE])->save();

        return true;
    }

    /**
     * Scope to persist the overdue rule across a query (safe + idempotent).
     * Includes legacy "unpaid" as SENT for backward compatibility.
     */
    public function scopeAutoMarkOverdue(Builder $query): Builder
    {
        $today = Carbon::today();

        (clone $query)
            ->whereIn('status', [self::STATUS_SENT, 'unpaid'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->update(['status' => self::STATUS_OVERDUE]);

        return $query;
    }

    /**
     * Route-model binding MUST be tenant-scoped.
     *
     * This prevents `Invoice $invoice` controller params from resolving invoices
     * belonging to other users.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $query = $this->newQuery();

        if (auth()->check() && ! optional(auth()->user())->isAdmin()) {
            $userId = (int) auth()->id();

            // Ownership: prefer invoices.user_id, fall back to client.user_id (legacy compatibility).
            $query->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhereHas('client', fn ($c) => $c->where('user_id', $userId));
            });
        }

        return $query
            ->where($field ?? $this->getRouteKeyName(), $value)
            ->first();
    }
}

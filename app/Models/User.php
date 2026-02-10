<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Client;
use App\Models\Invoice;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Schema;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'plan',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'pro_expires_at' => 'datetime',
            'pro_ends_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Role helpers
    |--------------------------------------------------------------------------
    */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isClient(): bool
    {
        // Backward compatible: "owner" is the new name for legacy "client" users.
        return in_array($this->role, ['client', 'owner'], true);
    }

    /**
     * Plan-only Pro check (no expiry logic).
     * Missing/null plan is treated as "free".
     */
    public function isPlanPro(): bool
    {
        return ($this->plan ?? 'free') === 'pro';
    }

    /*
    |--------------------------------------------------------------------------
    | Pro / Plan logic — V2.1 SAFE
    |--------------------------------------------------------------------------
    | OPTION A:
    | - pro_expires_at = NULL  => Pro (legacy / safe default)
    | - future date            => Pro
    | - past date              => Free
    */
    public function isPro(): bool
    {
        // Prefer the newer pro_ends_at column when present; fall back to pro_expires_at (legacy).
        $endsAt = $this->pro_ends_at ?? $this->pro_expires_at;

        if ($endsAt instanceof CarbonInterface) {
            return $endsAt->isFuture();
        }

        // If there's no expiry timestamp, treat plan='pro' as lifetime Pro (manual/admin).
        return ($this->plan ?? 'free') === 'pro';
    }

    public function isProExpired(): bool
    {
        $endsAt = $this->pro_ends_at ?? $this->pro_expires_at;

        return $endsAt instanceof CarbonInterface
            && $endsAt->isPast();
    }

    public function grantProForDays(int $days): self
    {
        if ($days <= 0) {
            return $this;
        }

        $until = now()->addDays($days);

        // Keep both columns in sync when available.
        if (Schema::hasColumn('users', 'pro_ends_at')) {
            $this->pro_ends_at = $until;
        }
        if (Schema::hasColumn('users', 'pro_expires_at')) {
            $this->pro_expires_at = $until;
        }

        if (Schema::hasColumn('users', 'plan')) {
            $this->plan = 'pro';
        }
        $this->save();

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Reminder limits (plan-based)
    |--------------------------------------------------------------------------
    */
    public function hasReachedActiveReminderLimit(
        int $limit = 5,
        ?int $excludingClientId = null
    ): bool {
        if ($this->isAdmin() || $this->isPro()) {
            return false;
        }

        $query = Client::query()
            ->where('user_id', $this->id)
            ->where('reminder_enabled', true);

        if ($excludingClientId !== null) {
            $query->where('id', '!=', $excludingClientId);
        }

        return $query->count() >= $limit;
    }
}

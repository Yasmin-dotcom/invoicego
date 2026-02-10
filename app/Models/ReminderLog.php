<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReminderLog extends Model
{
    protected $table = 'reminder_logs';

    protected $fillable = [
        'invoice_id',
        'client_id',
        'reminder_type',
        'status',
        'channel',
        'reason',
        'error_message',
        'sent_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Centralized, crash-safe logging for reminder outcomes.
     * Used by cron + manual reminder sending.
     */
    public static function logOutcome(
        Invoice $invoice,
        string $status,
        string $channel,
        ?string $reason = null,
        ?string $reminderType = null,
        ?string $errorMessage = null
    ): void {
        try {
            $now = now();

            DB::table('reminder_logs')->insert([
                'invoice_id' => $invoice->id,
                'client_id' => $invoice->client_id,
                'reminder_type' => $reminderType,
                'channel' => $channel,
                'status' => $status,
                'reason' => $reason,
                'error_message' => $errorMessage,
                'sent_at' => $status === 'sent' ? $now : null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } catch (\Throwable $e) {
            // Never break reminder sending if logging fails
            Log::warning('ReminderLog::logOutcome failed', [
                'invoice_id' => $invoice->id,
                'status' => $status,
                'channel' => $channel,
                'reason' => $reason,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

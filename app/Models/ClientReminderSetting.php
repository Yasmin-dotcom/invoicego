<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientReminderSetting extends Model
{
    protected $table = 'client_reminder_settings';

    protected $fillable = [
        'client_id',
        'reminders_enabled',
        'reminder_days',
        'email_enabled',
        'whatsapp_enabled',
        'sms_enabled',
    ];

    protected $casts = [
        'reminders_enabled' => 'boolean',
        'reminder_days' => 'array',
        'email_enabled' => 'boolean',
        'whatsapp_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}

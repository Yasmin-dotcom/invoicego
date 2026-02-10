<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'address',
        'reminder_enabled',
        'reminder_channel',
    ];

    protected $casts = [
        'reminder_enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reminderSetting(): HasOne
    {
        return $this->hasOne(ClientReminderSetting::class);
    }

    /**
     * Merge admin defaults + client overrides to produce the effective reminder config.
     *
     * Nullable fields in client_reminder_settings mean "use admin default".
     * Admin reminders_enabled=false always wins (system-wide block).
     */
    public function getEffectiveReminderSettings(?SettingsReminder $admin = null): array
    {
        $admin ??= SettingsReminder::current();
        $override = $this->relationLoaded('reminderSetting')
            ? $this->reminderSetting
            : $this->reminderSetting()->first();

        $adminEnabled = (bool) $admin->reminders_enabled;

        $clientEnabledOverride = $override?->reminders_enabled; // bool|null
        $effectiveEnabled = $adminEnabled && ($clientEnabledOverride ?? true);

        $effectiveDays = $override?->reminder_days ?? ($admin->default_reminder_days ?? [3, 0, -2]);

        $effectiveEmail = (bool) $admin->email_enabled && (($override?->email_enabled) ?? true);
        $effectiveWhatsapp = (bool) $admin->whatsapp_enabled && (($override?->whatsapp_enabled) ?? true);
        $effectiveSms = (bool) $admin->sms_enabled && (($override?->sms_enabled) ?? true);

        return [
            'reminders_enabled' => $effectiveEnabled,
            'reminder_days' => $effectiveDays,
            'email_enabled' => $effectiveEmail,
            'whatsapp_enabled' => $effectiveWhatsapp,
            'sms_enabled' => $effectiveSms,
            'admin_reminders_enabled' => $adminEnabled,
        ];
    }
}

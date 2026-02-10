<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SettingsReminder extends Model
{
    protected $table = 'settings_reminders';

    protected $fillable = [
        'reminders_enabled',
        'default_reminder_days',
        'email_enabled',
        'whatsapp_enabled',
        'sms_enabled',
    ];

    protected $casts = [
        'reminders_enabled' => 'boolean',
        'default_reminder_days' => 'array',
        'email_enabled' => 'boolean',
        'whatsapp_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
    ];

    /**
     * Single-row settings accessor (cached forever for fast admin page loads).
     */
    public static function current(): self
    {
        return Cache::rememberForever('settings_reminders', function () {
            return self::firstOrCreate([], [
                'reminders_enabled' => true,
                'default_reminder_days' => [3, 0, -2],
                'email_enabled' => true,
                'whatsapp_enabled' => false,
                'sms_enabled' => false,
            ]);
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('settings_reminders');
    }
}

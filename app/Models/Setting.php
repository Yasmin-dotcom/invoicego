<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'reminders_enabled',
        'free_reminder_limit',
        'pro_reminder_limit',
    ];

    protected $casts = [
        'reminders_enabled' => 'boolean',
        'free_reminder_limit' => 'integer',
        'pro_reminder_limit' => 'integer',
    ];

    /**
     * Get settings from cache or database.
     * Uses rememberForever for maximum performance.
     */
    public static function getSettings(): self
    {
        return Cache::rememberForever('app_settings', function () {
            return self::firstOrCreate([], [
                'reminders_enabled' => true,
                'free_reminder_limit' => 5,
                'pro_reminder_limit' => 9999,
            ]);
        });
    }

    /**
     * Clear settings cache.
     * Call this after updating settings.
     */
    public static function clearCache(): void
    {
        Cache::forget('app_settings');
    }
}

<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientReminderSetting;
use App\Models\SettingsReminder;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ReminderSettingController extends Controller
{
    public function edit(): View
    {
        $client = Auth::user()
            ->clients()
            ->with('reminderSetting')
            ->first();
        
        if (! $client) {
            abort(404, 'Client record not found.');
        }

        // Cached single-row admin defaults
        $adminSettings = SettingsReminder::current();

        // Effective config (admin defaults + client overrides)
        $effective = $client->getEffectiveReminderSettings($adminSettings);

        return view('client.settings.reminders', [
            'client' => $client,
            'adminSettings' => $adminSettings,
            'override' => $client->reminderSetting,
            'effective' => $effective,
        ]);
    }

    /**
     * Update client reminder settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $client = $user
            ->clients()
            ->with('reminderSetting')
            ->first();
        
        if (! $client) {
            abort(404, 'Client record not found.');
        }

        $adminSettings = SettingsReminder::current();

        $validated = $request->validate([
            'reminders_enabled' => ['nullable'],
            'reminder_days' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^\s*-?\d+\s*(\s*,\s*-?\d+\s*)*$/',
            ],
            'email_enabled' => ['nullable'],
            'whatsapp_enabled' => ['nullable'],
            'sms_enabled' => ['nullable'],
        ]);

        $parseNullableBool = function ($value): ?bool {
            if ($value === null || $value === '') {
                return null;
            }
            return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        };

        $overrides = [
            'reminders_enabled' => $parseNullableBool($validated['reminders_enabled'] ?? null),
            'email_enabled' => $parseNullableBool($validated['email_enabled'] ?? null),
            'whatsapp_enabled' => $parseNullableBool($validated['whatsapp_enabled'] ?? null),
            'sms_enabled' => $parseNullableBool($validated['sms_enabled'] ?? null),
        ];

        // reminder_days parsing
        $daysRaw = $validated['reminder_days'] ?? null;
        if ($daysRaw === null || trim($daysRaw) === '') {
            $overrides['reminder_days'] = null;
        } else {
            $overrides['reminder_days'] = collect(explode(',', $daysRaw))
                ->map(fn ($v) => (int) trim($v))
                ->unique()
                ->values()
                ->all();
        }

        /**
         * 🔒 V2.2 — Reminder limit enforcement (settings screen)
         * Applies ONLY when trying to enable reminders.
         */
        if (
            $overrides['reminders_enabled'] === true
            && ! $user->isPro()
            && $user->hasReachedActiveReminderLimit(5, $client->id)
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Free plan allows only 5 active reminders. Upgrade to Pro to enable more.');
        }

        // Admin master switch protection
        if (! $adminSettings->reminders_enabled && $overrides['reminders_enabled'] === true) {
            $overrides['reminders_enabled'] = null;
        }

        $setting = $client->reminderSetting
            ?: new ClientReminderSetting(['client_id' => $client->id]);

        $setting->fill($overrides);

        $allNull = $setting->reminders_enabled === null
            && $setting->reminder_days === null
            && $setting->email_enabled === null
            && $setting->whatsapp_enabled === null
            && $setting->sms_enabled === null;

        if ($allNull) {
            if ($setting->exists) {
                $setting->delete();
            }
        } else {
            $setting->save();
        }

        return redirect()
            ->route('client.settings.reminders')
            ->with('success', 'Client reminder settings updated.');
    }
}

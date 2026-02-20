<?php

namespace App\Http\Controllers;

use App\Models\SettingsReminder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function reminders(): View
    {
        $settings = SettingsReminder::query()->find(1) ?? SettingsReminder::current();

        return view('settings.reminder', compact('settings'));
    }

    public function saveReminders(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'enable_reminders' => ['nullable', 'boolean'],
            'start_after_days' => ['required', 'integer', 'min:0', 'max:365'],
            'repeat_every_days' => ['required', 'integer', 'min:1', 'max:365'],
            'max_reminders' => ['required', 'integer', 'min:1', 'max:100'],
            'email_enabled' => ['nullable', 'boolean'],
            'whatsapp_enabled' => ['nullable', 'boolean'],
            'sms_enabled' => ['nullable', 'boolean'],
        ]);

        $settings = SettingsReminder::query()->firstOrCreate(
            ['id' => 1],
            [
                'reminders_enabled' => true,
                'start_after_days' => 0,
                'repeat_every_days' => 3,
                'max_reminders' => 5,
                'default_reminder_days' => [3, 0, -2],
                'email_enabled' => true,
                'whatsapp_enabled' => false,
                'sms_enabled' => false,
            ]
        );

        $settings->update([
            'reminders_enabled' => (bool) ($validated['enable_reminders'] ?? false),
            'start_after_days' => (int) $validated['start_after_days'],
            'repeat_every_days' => (int) $validated['repeat_every_days'],
            'max_reminders' => (int) $validated['max_reminders'],
            'email_enabled' => (bool) ($validated['email_enabled'] ?? false),
            'whatsapp_enabled' => (bool) ($validated['whatsapp_enabled'] ?? false),
            'sms_enabled' => (bool) ($validated['sms_enabled'] ?? false),
        ]);

        SettingsReminder::clearCache();

        return redirect()
            ->route('settings.reminders')
            ->with('success', 'Reminder settings saved.');
    }
}

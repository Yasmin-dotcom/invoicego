<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateReminderSettingsRequest;
use App\Models\SettingsReminder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReminderSettingsController extends Controller
{
    public function index(): View
    {
        // Cached single-row settings (fast page load, no repeated DB hits)
        $settings = SettingsReminder::current();

        return view('admin.reminder-settings', compact('settings'));
    }

    public function update(UpdateReminderSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Normalize "3,0,-2" -> [3, 0, -2]
        $days = collect(explode(',', $validated['default_reminder_days']))
            ->map(fn ($v) => (int) trim($v))
            ->unique()
            ->values()
            ->all();

        $settings = SettingsReminder::firstOrCreate([], [
            'reminders_enabled' => true,
            'default_reminder_days' => [3, 0, -2],
            'email_enabled' => true,
            'whatsapp_enabled' => false,
            'sms_enabled' => false,
        ]);

        $settings->update([
            'reminders_enabled' => (bool) $validated['reminders_enabled'],
            'default_reminder_days' => $days,
            'email_enabled' => (bool) $validated['email_enabled'],
            'whatsapp_enabled' => (bool) $validated['whatsapp_enabled'],
            'sms_enabled' => (bool) $validated['sms_enabled'],
        ]);

        // Clear cache so next request reflects latest settings
        SettingsReminder::clearCache();

        return redirect()
            ->route('admin.reminder-settings')
            ->with('success', 'Reminder settings saved.');
    }
}

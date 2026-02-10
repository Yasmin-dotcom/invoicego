<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReminderSettingController extends Controller
{
    /**
     * Display reminder settings page.
     * Uses cached settings for fast page load.
     */
    public function index(): View
    {
        // Get settings from cache (no DB query on every request)
        $settings = Setting::getSettings();

        return view('admin.settings.reminders', compact('settings'));
    }

    /**
     * Update reminder settings.
     * Validates, saves, and clears cache.
     * Supports both form submission and AJAX requests.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'reminders_enabled' => ['required'],
            'free_reminder_limit' => ['required', 'integer', 'min:0', 'max:100'],
            'pro_reminder_limit' => ['required', 'integer', 'min:1', 'max:100000'],
        ]);

        // Convert reminders_enabled to boolean (handles '1', '0', true, false)
        $validated['reminders_enabled'] = filter_var($validated['reminders_enabled'], FILTER_VALIDATE_BOOLEAN);

        // Get or create settings record
        $settings = Setting::firstOrCreate([], [
            'reminders_enabled' => true,
            'free_reminder_limit' => 5,
            'pro_reminder_limit' => 9999,
        ]);

        // Update settings
        $settings->update($validated);

        // Clear cache so next request fetches fresh data
        Setting::clearCache();

        // Return JSON for AJAX requests, redirect for form submissions
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reminder settings updated successfully.',
            ]);
        }

        return redirect()
            ->route('admin.settings.reminders')
            ->with('success', 'Reminder settings updated successfully.');
    }
}

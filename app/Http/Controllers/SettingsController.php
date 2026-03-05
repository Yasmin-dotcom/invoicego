<?php

namespace App\Http\Controllers;

use App\Models\SettingsReminder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Reminder Settings (UNCHANGED)
    |--------------------------------------------------------------------------
    */

    public function reminders(): View
    {
        $settings = SettingsReminder::query()->find(1) ?? SettingsReminder::current();
        return view('settings.reminder', compact('settings'));
    }

    public function saveReminders(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'enable_reminders'     => ['nullable', 'boolean'],
            'start_after_days'     => ['required', 'integer', 'min:0', 'max:365'],
            'repeat_every_days'    => ['required', 'integer', 'min:1', 'max:365'],
            'max_reminders'        => ['required', 'integer', 'min:1', 'max:100'],
            'email_enabled'        => ['nullable', 'boolean'],
            'whatsapp_enabled'     => ['nullable', 'boolean'],
            'sms_enabled'          => ['nullable', 'boolean'],
        ]);

        $settings = SettingsReminder::query()->firstOrCreate(
            ['id' => 1],
            [
                'reminders_enabled' => true,
                'start_after_days'  => 0,
                'repeat_every_days' => 3,
                'max_reminders'     => 5,
                'default_reminder_days' => [3, 0, -2],
                'email_enabled'     => true,
                'whatsapp_enabled'  => false,
                'sms_enabled'       => false,
            ]
        );

        $settings->update([
            'reminders_enabled' => (bool) ($validated['enable_reminders'] ?? false),
            'start_after_days'  => (int) $validated['start_after_days'],
            'repeat_every_days' => (int) $validated['repeat_every_days'],
            'max_reminders'     => (int) $validated['max_reminders'],
            'email_enabled'     => (bool) ($validated['email_enabled'] ?? false),
            'whatsapp_enabled'  => (bool) ($validated['whatsapp_enabled'] ?? false),
            'sms_enabled'       => (bool) ($validated['sms_enabled'] ?? false),
        ]);

        SettingsReminder::clearCache();

        return redirect()
            ->route('settings.reminders')
            ->with('success', 'Reminder settings saved.');
    }

    /*
    |--------------------------------------------------------------------------
    | Template Settings (PER USER)
    |--------------------------------------------------------------------------
    */

    public function templates(): View
    {
        $user = auth()->user();

        return view('settings.templates', [
            'settings' => (object)[
                'default_template' => $user->default_template ?? 'classic'
            ]
        ]);
    }

    /**
     * Save Default Template (Per User Safe)
     */
    public function saveTemplate(Request $request): RedirectResponse
    {
        $allowedTemplates = ['classic','minimal','modern','gst','premium'];
        $freeAllowed      = ['classic','minimal'];

        $validated = $request->validate([
            'default_template' => ['required', 'in:' . implode(',', $allowedTemplates)],
        ]);

        $selected = $validated['default_template'];
        $user     = auth()->user();
        $isPro    = $user?->isPro();

        // Free user protection
        if (!$isPro && !in_array($selected, $freeAllowed)) {
            return back()->with('error', 'Upgrade to Pro to use this template.');
        }

        // ✅ SAVE TO USERS TABLE
        $user->default_template = $selected;
        $user->save();

        return back()->with('success', 'Default invoice template updated.');
    }

    /**
     * Safe Template Preview
     */
    public function previewTemplate(string $template)
    {
        $allowedTemplates = ['classic','minimal','modern','gst','premium'];

        if (!in_array($template, $allowedTemplates)) {
            abort(404);
        }

        if (!view()->exists("invoices.templates.$template")) {
            abort(404);
        }
        
        $businessUser = auth()->user();
        $fakeInvoice = (object) [
            'template_name'  => $template,
            'invoice_number' => 'INV-DEMO-001',
            'invoice_date'   => now(),
            'due_date'       => now()->addDays(7),
            'paid_at'        => null,
            'status'         => 'unpaid',
            'client'         => (object) ['name' => 'Demo Client'],
            'items'          => [
                (object)[
                    'description' => 'Sample Product',
                    'quantity'    => 2,
                    'price'       => 499,
                    'cgst'        => 44.91,
                    'sgst'        => 44.91,
                    'igst'        => 0,
                ],
            ],
            'total'       => 998,
            'cgst_total'  => 89.82,
            'sgst_total'  => 89.82,
            'igst_total'  => 0,
            'grand_total' => 1177.64,
        ];

        return Pdf::loadView("invoices.templates.$template", [
            'invoice' => $fakeInvoice,
            'businessUser' => $businessUser,
        ])->stream("preview-$template.pdf");
    }
}
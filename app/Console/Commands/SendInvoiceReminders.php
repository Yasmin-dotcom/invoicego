<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\Invoice;
use App\Models\ReminderLog;
use App\Models\SettingsReminder;
use App\Mail\InvoicePaymentReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SendInvoiceReminders extends Command
{
    protected $signature = 'app:send-invoice-reminders {--overdue : Only send reminders for overdue invoices}';

    protected $description = 'Send overdue invoice reminders based on global settings';

    public function handle()
    {
        $this->info('Starting overdue reminder process...');

        $settings = Setting::getSettings();

        if (! $settings || ! $settings->reminders_enabled) {
            $this->warn('Reminders are disabled by admin.');
            return Command::SUCCESS;
        }

        $global = SettingsReminder::current();
        if (! $global->reminders_enabled) {
            $this->warn('Global reminder settings are disabled.');
            return Command::SUCCESS;
        }

        $today = Carbon::today();
        $startAfterDays = max(0, (int) ($global->start_after_days ?? 0));
        $repeatEveryDays = max(1, (int) ($global->repeat_every_days ?? 3));
        $maxReminders = max(1, (int) ($global->max_reminders ?? 5));

        Invoice::query()->autoMarkOverdue();

        $candidates = Invoice::with(['client'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->whereNull('paid_at')
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhereRaw('LOWER(status) != ?', [Invoice::STATUS_PAID]);
            })
            ->get();

        if ($candidates->isEmpty()) {
            $this->warn('No overdue unpaid invoices eligible for reminders.');
            return Command::SUCCESS;
        }

        $this->info("Found {$candidates->count()} invoice(s) eligible for reminders.");

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($candidates as $invoice) {
            $reminderType = 'overdue';

            if (! $invoice->client || ! $invoice->client->email) {
                ReminderLog::logOutcome($invoice, 'skipped', 'email', 'email_missing', $reminderType);
                $skippedCount++;
                continue;
            }

            if (! $invoice->client->reminder_enabled) {
                ReminderLog::logOutcome($invoice, 'skipped', 'email', 'client_disabled', $reminderType);
                $skippedCount++;
                continue;
            }

            $effective = $invoice->client->getEffectiveReminderSettings($global);
            if (! ($effective['reminders_enabled'] ?? true) || ! ($effective['email_enabled'] ?? true)) {
                ReminderLog::logOutcome($invoice, 'skipped', 'email', 'reminders_disabled', $reminderType);
                $skippedCount++;
                continue;
            }

            $daysOverdue = (int) $invoice->due_date->diffInDays($today);
            if ($daysOverdue < $startAfterDays) {
                ReminderLog::logOutcome($invoice, 'skipped', 'email', 'start_after_not_reached', $reminderType);
                $skippedCount++;
                continue;
            }

            if ((int) $invoice->reminder_count >= $maxReminders) {
                ReminderLog::logOutcome($invoice, 'skipped', 'email', 'limit_reached', $reminderType);
                $skippedCount++;
                continue;
            }

            $lastReminded = $invoice->last_reminded_at ? Carbon::parse($invoice->last_reminded_at) : null;
            if ($lastReminded && $lastReminded->diffInDays($today) < $repeatEveryDays) {
                ReminderLog::logOutcome($invoice, 'skipped', 'email', 'repeat_interval_pending', $reminderType);
                $skippedCount++;
                continue;
            }

            try {
                DB::transaction(function () use ($invoice, $reminderType) {
                    Mail::to($invoice->client->email)
                        ->send(new InvoicePaymentReminderMail($invoice, $reminderType));

                    $invoice->increment('reminder_count');
                    $invoice->update(['last_reminded_at' => now()]);
                });

                $this->info("Reminder sent → {$invoice->invoice_number}");
                Log::info('Reminder sent for invoice ' . $invoice->invoice_number);
                ReminderLog::logOutcome($invoice, 'sent', 'email', null, $reminderType);
                $sentCount++;
            } catch (\Throwable $e) {
                Log::error('Reminder failed', [
                    'invoice_id' => $invoice->id,
                    'error' => $e->getMessage(),
                ]);
                ReminderLog::logOutcome($invoice, 'failed', 'email', 'exception', $reminderType, $e->getMessage());
                $skippedCount++;
            }
        }

        $this->info("Done: {$sentCount} sent, {$skippedCount} skipped.");

        return Command::SUCCESS;
    }
}

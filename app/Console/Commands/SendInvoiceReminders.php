<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\Invoice;
use App\Models\ReminderLog;
use App\Mail\InvoicePaymentReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SendInvoiceReminders extends Command
{
    protected $signature = 'app:send-invoice-reminders {--overdue : Only send reminders for overdue invoices}';

    protected $description = 'Send payment reminder emails for unpaid invoices';

    public function handle()
    {
        $this->info('Starting payment reminder process...');

        /**
         * ------------------------------------------------------------
         * ADMIN MASTER TOGGLE
         * ------------------------------------------------------------
         */
        $settings = Setting::getSettings();

        if (!$settings || !$settings->reminders_enabled) {
            $this->warn('Reminders are disabled by admin.');

            // Log skipped outcomes for overdue unpaid invoices (best effort, does not alter send logic).
            $today = Carbon::today();
            Invoice::select(['id', 'client_id'])
                ->whereIn('status', [Invoice::STATUS_SENT, Invoice::STATUS_OVERDUE, 'unpaid'])
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', $today)
                ->orderBy('id')
                ->chunkById(250, function ($chunk) {
                    foreach ($chunk as $inv) {
                        ReminderLog::logOutcome($inv, 'skipped', 'email', 'admin_disabled', 'after_due');
                    }
                });

            return Command::SUCCESS;
        }

        /**
         * ------------------------------------------------------------
         * CONFIG
         * ------------------------------------------------------------
         */
        $minGapDays   = 3;
        $maxReminders = config('reminders.max_reminders', 5);
        $today        = Carbon::today();

        /**
         * ------------------------------------------------------------
         * FETCH OVERDUE UNPAID INVOICES
         * ------------------------------------------------------------
         */
        // Persist lifecycle overdue rule before fetching (includes legacy "unpaid" as SENT)
        Invoice::query()->autoMarkOverdue();

        $query = Invoice::with('client')
            ->whereIn('status', [Invoice::STATUS_SENT, Invoice::STATUS_OVERDUE, 'unpaid'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today);

        if ($this->option('overdue')) {
            $this->info('Filtering for overdue invoices only...');
        }

        $invoices = $query->get();

        if ($invoices->isEmpty()) {
            $this->warn('No unpaid invoices found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$invoices->count()} unpaid invoice(s).");

        $sentCount = 0;
        $skippedCount = 0;

        /**
         * ------------------------------------------------------------
         * PROCESS INVOICES
         * ------------------------------------------------------------
         */
        foreach ($invoices as $invoice) {

            if (!$invoice->client || !$invoice->client->email) {
                ReminderLog::logOutcome($invoice, 'skipped', 'email', 'email_missing', 'after_due');
                $skippedCount++;
                continue;
            }

            if (!$invoice->client->reminder_enabled) {
                ReminderLog::logOutcome($invoice, 'skipped', 'email', 'client_disabled', 'after_due');
                $skippedCount++;
                continue;
            }

            if ($invoice->reminder_count >= $maxReminders) {
                ReminderLog::logOutcome($invoice, 'skipped', 'email', 'limit_reached', 'after_due');
                $skippedCount++;
                continue;
            }

            $lastReminded = $invoice->last_reminded_at
                ? Carbon::parse($invoice->last_reminded_at)
                : null;

            if ($lastReminded && $lastReminded->diffInDays($today) < $minGapDays) {
                ReminderLog::logOutcome($invoice, 'skipped', 'email', 'gap_not_met', 'after_due');
                $skippedCount++;
                continue;
            }

            /**
             * ------------------------------------------------------------
             * SEND EMAIL
             * ------------------------------------------------------------
             */
            try {
                DB::transaction(function () use ($invoice) {
                    Mail::to($invoice->client->email)
                        ->send(new InvoicePaymentReminderMail($invoice, 'overdue'));

                    $invoice->increment('reminder_count');
                    $invoice->update([
                        'last_reminded_at' => now(),
                    ]);
                });

                $this->info("Reminder sent → {$invoice->invoice_number}");
                ReminderLog::logOutcome($invoice, 'sent', 'email', null, 'after_due');
                $sentCount++;

            } catch (\Throwable $e) {
                Log::error('Reminder failed', [
                    'invoice_id' => $invoice->id,
                    'error' => $e->getMessage(),
                ]);

                // We keep DB error_message logging inside ReminderLog model to remain crash-safe.
                ReminderLog::logOutcome($invoice, 'failed', 'email', 'exception', 'after_due', $e->getMessage());
                $skippedCount++;
            }
        }

        $this->info("Done: {$sentCount} sent, {$skippedCount} skipped.");

        return Command::SUCCESS;
    }
}

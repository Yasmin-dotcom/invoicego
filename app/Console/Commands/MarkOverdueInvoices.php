<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkOverdueInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:mark-overdue-invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark SENT invoices as OVERDUE when due_date is past and unpaid';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        $updated = Invoice::query()
            ->where('status', Invoice::STATUS_SENT)
            ->whereNull('paid_at')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->update(['status' => Invoice::STATUS_OVERDUE]);

        $this->info("Marked {$updated} invoice(s) as overdue.");

        return self::SUCCESS;
    }
}

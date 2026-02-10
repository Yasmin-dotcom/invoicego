<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Add invoice_date only if it doesn't exist
            if (!Schema::hasColumn('invoices', 'invoice_date')) {
                $table->date('invoice_date')
                      ->nullable()
                      ->after('invoice_number');
            }

            // Add due_date only if it doesn't exist
            if (!Schema::hasColumn('invoices', 'due_date')) {
                $table->date('due_date')
                      ->nullable()
                      ->after('invoice_date');
            }

            // Add status only if it doesn't exist
            if (!Schema::hasColumn('invoices', 'status')) {
                $table->string('status')
                      ->default('unpaid')
                      ->after('total');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $columnsToDrop = [];

            // Only drop columns that exist
            if (Schema::hasColumn('invoices', 'invoice_date')) {
                $columnsToDrop[] = 'invoice_date';
            }

            if (Schema::hasColumn('invoices', 'due_date')) {
                $columnsToDrop[] = 'due_date';
            }

            if (Schema::hasColumn('invoices', 'status')) {
                $columnsToDrop[] = 'status';
            }

            // Drop columns only if there are any to drop
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};

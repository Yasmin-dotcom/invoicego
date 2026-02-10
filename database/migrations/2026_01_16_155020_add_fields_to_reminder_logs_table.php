<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reminder_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('reminder_logs', 'reminder_type')) {
                $table->string('reminder_type', 50)->nullable()->after('client_id');
            }

            if (!Schema::hasColumn('reminder_logs', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('reason');
            }

            if (!Schema::hasColumn('reminder_logs', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });

        // Indexes: existing migration already indexes client_id + status; we add what's missing.
        // Use best-effort creation to avoid crashing on duplicate index names across DB engines.
        try {
            if (Schema::hasColumn('reminder_logs', 'invoice_id')) {
                DB::statement('CREATE INDEX IF NOT EXISTS reminder_logs_invoice_id_index ON reminder_logs (invoice_id)');
            }
        } catch (\Throwable $e) {
            // ignore
        }

        try {
            if (Schema::hasColumn('reminder_logs', 'sent_at')) {
                DB::statement('CREATE INDEX IF NOT EXISTS reminder_logs_sent_at_index ON reminder_logs (sent_at)');
            }
        } catch (\Throwable $e) {
            // ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reminder_logs', function (Blueprint $table) {
            if (Schema::hasColumn('reminder_logs', 'reminder_type')) {
                $table->dropColumn('reminder_type');
            }

            if (Schema::hasColumn('reminder_logs', 'sent_at')) {
                $table->dropColumn('sent_at');
            }

            if (Schema::hasColumn('reminder_logs', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });

        // Best-effort cleanup of indexes (SQLite supports IF EXISTS)
        try {
            DB::statement('DROP INDEX IF EXISTS reminder_logs_invoice_id_index');
        } catch (\Throwable $e) {
            // ignore
        }

        try {
            DB::statement('DROP INDEX IF EXISTS reminder_logs_sent_at_index');
        } catch (\Throwable $e) {
            // ignore
        }
    }
};

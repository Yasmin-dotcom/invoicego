<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('status');
            }

            if (! Schema::hasColumn('invoices', 'viewed_at')) {
                $table->timestamp('viewed_at')->nullable()->after('sent_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'viewed_at')) {
                $table->dropColumn('viewed_at');
            }

            if (Schema::hasColumn('invoices', 'sent_at')) {
                $table->dropColumn('sent_at');
            }
        });
    }
};

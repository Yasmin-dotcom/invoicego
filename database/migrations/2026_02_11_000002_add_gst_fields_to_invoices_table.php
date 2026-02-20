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
            $table->decimal('cgst_total', 10, 2)->nullable()->after('total');
            $table->decimal('sgst_total', 10, 2)->nullable()->after('cgst_total');
            $table->decimal('igst_total', 10, 2)->nullable()->after('sgst_total');
            $table->decimal('grand_total', 10, 2)->nullable()->after('igst_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['cgst_total', 'sgst_total', 'igst_total', 'grand_total']);
        });
    }
};

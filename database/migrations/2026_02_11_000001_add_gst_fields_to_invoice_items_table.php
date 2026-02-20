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
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('gst_rate', 5, 2)->nullable()->after('price');
            $table->decimal('cgst', 10, 2)->nullable()->after('gst_rate');
            $table->decimal('sgst', 10, 2)->nullable()->after('cgst');
            $table->decimal('igst', 10, 2)->nullable()->after('sgst');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn(['gst_rate', 'cgst', 'sgst', 'igst']);
        });
    }
};

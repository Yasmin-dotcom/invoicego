<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Safely ensures users.invoice_prefix exists as VARCHAR(50), nullable.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'invoice_prefix')) {
                $table->string('invoice_prefix', 50)->nullable()->change();
            } else {
                $table->string('invoice_prefix', 50)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * No-op to avoid risking data loss; existing data remains valid
     * even if the column stays at length 50.
     */
    public function down(): void
    {
        // Intentionally left blank for safety.
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds Phase-1 business/bank columns to users table only if they do not exist.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'business_address')) {
                $table->text('business_address')->nullable()->after('state_code');
            }
            if (!Schema::hasColumn('users', 'business_city')) {
                $table->string('business_city')->nullable();
            }
            if (!Schema::hasColumn('users', 'business_state')) {
                $table->string('business_state')->nullable();
            }
            if (!Schema::hasColumn('users', 'business_pincode')) {
                $table->string('business_pincode', 10)->nullable();
            }
            if (!Schema::hasColumn('users', 'bank_name')) {
                $table->string('bank_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'bank_branch')) {
                $table->string('bank_branch')->nullable();
            }
            if (!Schema::hasColumn('users', 'bank_account_name')) {
                $table->string('bank_account_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'bank_account_number')) {
                $table->string('bank_account_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'bank_ifsc')) {
                $table->string('bank_ifsc', 20)->nullable();
            }
            if (!Schema::hasColumn('users', 'invoice_prefix')) {
                $table->string('invoice_prefix', 10)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'business_address', 'business_city', 'business_state', 'business_pincode',
                'bank_name', 'bank_branch', 'bank_account_name', 'bank_account_number',
                'bank_ifsc', 'invoice_prefix',
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('name');
            $table->string('logo_path')->nullable()->after('business_name');
            $table->string('currency', 10)->default('INR')->after('logo_path');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['business_name', 'logo_path', 'currency']);
        });
    }
};

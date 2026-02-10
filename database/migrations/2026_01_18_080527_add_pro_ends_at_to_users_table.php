<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'pro_ends_at')) {
                // ❌ after('plan') removed because plan column does NOT exist
                $table->timestamp('pro_ends_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'pro_ends_at')) {
                $table->dropColumn('pro_ends_at');
            }
        });
    }
};

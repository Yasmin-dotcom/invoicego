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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_pro')) {
                $table->boolean('is_pro')->default(false)->after('role');
            }

            if (!Schema::hasColumn('users', 'pro_expires_at')) {
                $table->timestamp('pro_expires_at')->nullable()->after('is_pro');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'pro_expires_at')) {
                $table->dropColumn('pro_expires_at');
            }

            if (Schema::hasColumn('users', 'is_pro')) {
                $table->dropColumn('is_pro');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings_reminders', function (Blueprint $table) {
            if (! Schema::hasColumn('settings_reminders', 'start_after_days')) {
                $table->unsignedInteger('start_after_days')->default(0)->after('reminders_enabled');
            }

            if (! Schema::hasColumn('settings_reminders', 'repeat_every_days')) {
                $table->unsignedInteger('repeat_every_days')->default(3)->after('start_after_days');
            }

            if (! Schema::hasColumn('settings_reminders', 'max_reminders')) {
                $table->unsignedInteger('max_reminders')->default(5)->after('repeat_every_days');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings_reminders', function (Blueprint $table) {
            if (Schema::hasColumn('settings_reminders', 'max_reminders')) {
                $table->dropColumn('max_reminders');
            }

            if (Schema::hasColumn('settings_reminders', 'repeat_every_days')) {
                $table->dropColumn('repeat_every_days');
            }

            if (Schema::hasColumn('settings_reminders', 'start_after_days')) {
                $table->dropColumn('start_after_days');
            }
        });
    }
};

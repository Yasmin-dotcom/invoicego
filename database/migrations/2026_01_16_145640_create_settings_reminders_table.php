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
        if (Schema::hasTable('settings_reminders')) {
            return;
        }

        Schema::create('settings_reminders', function (Blueprint $table) {
            $table->id();

            $table->boolean('reminders_enabled')->default(true);

            // Example: [3, 0, -2] (3 days before, on due date, 2 days after)
            $table->json('default_reminder_days')->nullable();

            $table->boolean('email_enabled')->default(true);
            $table->boolean('whatsapp_enabled')->default(false);
            $table->boolean('sms_enabled')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings_reminders');
    }
};

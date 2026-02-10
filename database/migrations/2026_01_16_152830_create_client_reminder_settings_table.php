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
        Schema::create('client_reminder_settings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete()
                ->unique();

            // Nullable means "use admin default"
            $table->boolean('reminders_enabled')->nullable();
            $table->json('reminder_days')->nullable(); // Example: [3,0,-2]

            $table->boolean('email_enabled')->nullable();
            $table->boolean('whatsapp_enabled')->nullable();
            $table->boolean('sms_enabled')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_reminder_settings');
    }
};

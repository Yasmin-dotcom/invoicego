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
        Schema::create('reminder_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_id')
                ->nullable()
                ->constrained('invoices')
                ->nullOnDelete();

            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            // sent | skipped | failed
            $table->string('status', 20);

            // email | whatsapp (future-ready)
            $table->string('channel', 20)->default('email');

            // admin_disabled, client_disabled, limit_reached, before_due, due_today, overdue, error, etc.
            $table->string('reason', 100)->nullable();

            $table->text('error_message')->nullable();

            $table->timestamp('created_at')->useCurrent();

            // Indexes for analytics filters & fast pagination
            $table->index('client_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminder_logs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->index()
                ->constrained()
                ->nullOnDelete();

            $table->string('razorpay_order_id')->index();
            $table->string('razorpay_payment_id')->nullable();
            $table->string('razorpay_signature')->nullable();

            $table->unsignedInteger('amount'); // paise
            $table->string('currency')->default('INR');
            $table->string('status'); // created, paid, failed

            $table->json('raw_response')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};


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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->string('payment_method');
            $table->string('paybill_till_number')->nullable();
            $table->string('transaction_type');
            $table->decimal('transaction_amount', 15, 2);
            $table->decimal('transaction_fee', 15, 2)->default(0.00);
            $table->string('otp')->nullable();
            $table->string('sender_mobile');
            $table->string('receiver_mobile');
            $table->text('transaction_details')->nullable();
            $table->string('checkout_request_id')->nullable();
            $table->string('merchant_request_id')->nullable();
            $table->json('callback_metadata')->nullable();
            $table->string('result_desc')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

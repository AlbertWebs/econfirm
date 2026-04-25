<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('velipay_payments')) {
            return;
        }

        Schema::create('velipay_payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->nullable()->index();
            $table->string('velipay_payment_id', 120)->unique();
            $table->string('initiator_ip', 64)->nullable()->index();
            $table->string('phone', 30)->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('merchant_reference', 120)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('status', 50)->default('pending')->index();
            $table->string('failure_reason', 255)->nullable();
            $table->string('receipt_number', 120)->nullable();
            $table->json('raw_response')->nullable();
            $table->json('webhook_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('velipay_payments');
    }
};

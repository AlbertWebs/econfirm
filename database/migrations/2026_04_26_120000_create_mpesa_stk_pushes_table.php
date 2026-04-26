<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Recreates STK attempt logging after legacy M-Pesa tables were dropped.
     * Used by admin STK contacts sync and any future STK persistence.
     */
    public function up(): void
    {
        if (Schema::hasTable('mpesa_stk_pushes')) {
            return;
        }

        Schema::create('mpesa_stk_pushes', function (Blueprint $table) {
            $table->id();
            $table->string('initiator_ip', 45)->nullable();
            $table->string('phone', 32)->nullable()->index();
            $table->decimal('amount', 14, 2)->nullable();
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->string('checkout_request_id')->nullable();
            $table->string('merchant_request_id')->nullable();
            $table->string('response_code', 32)->nullable();
            $table->text('response_description')->nullable();
            $table->text('customer_message')->nullable();
            $table->string('status', 64)->nullable()->index();
            $table->text('result_desc')->nullable();
            $table->json('callback_metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpesa_stk_pushes');
    }
};

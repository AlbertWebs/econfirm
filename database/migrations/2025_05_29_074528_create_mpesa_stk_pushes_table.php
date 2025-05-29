<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mpesa_stk_pushes', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20);
            $table->decimal('amount', 10, 2);
            $table->string('reference')->nullable(); // AccountReference
            $table->string('description')->nullable(); // TransactionDesc
            $table->string('result_desc')->nullable();

            $table->string('checkout_request_id')->unique();
            $table->string('merchant_request_id')->nullable();

            $table->string('response_code')->nullable(); // 0 = success
            $table->string('response_description')->nullable();
            $table->string('customer_message')->nullable();

            $table->enum('status', ['Pending', 'Success', 'Failed'])->default('Pending');
            $table->json('callback_metadata')->nullable(); // Optional: store raw callback metadata

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpesa_stk_pushes');
    }
};


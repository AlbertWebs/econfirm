<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mpesa_c2b_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id', 64)->nullable()->index();
            $table->string('phone', 32)->nullable()->index();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('bill_reference_number', 128)->nullable()->index();
            $table->timestamp('transaction_time')->nullable()->index();
            $table->string('status', 32)->default('Received')->index();
            $table->string('mpesa_receipt_number', 64)->nullable()->index();
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mpesa_c2b_transactions');
    }
};

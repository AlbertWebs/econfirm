<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tariff_queries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('amount_kes')->nullable();
            $table->string('rail', 8)->nullable();
            $table->decimal('commission_kes', 14, 2)->nullable();
            $table->unsignedInteger('mpesa_fee_kes')->nullable();
            $table->unsignedBigInteger('total_kes')->nullable();
            $table->string('error_message', 512)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff_queries');
    }
};

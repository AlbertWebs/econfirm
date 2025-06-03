<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMpesaB2cCallbacksTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('mpesa_b2c_callbacks', function (Blueprint $table) {
            $table->id();
            $table->string('conversation_id')->nullable();
            $table->string('originator_conversation_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('result_type')->nullable();
            $table->string('result_code')->nullable();
            $table->string('result_desc')->nullable();
            $table->string('transaction_amount')->nullable();
            $table->string('transaction_receipt')->nullable();
            $table->string('receiver_party_public_name')->nullable();
            $table->string('transaction_completed_datetime')->nullable();
            $table->string('b2c_working_account_available_funds')->nullable();
            $table->string('b2c_utility_account_available_funds')->nullable();
            $table->string('b2c_charges_paid_account_available_funds')->nullable();
            $table->string('receiver_is_registered_customer')->nullable();
            $table->string('charges_paid')->nullable();
            $table->string('queue_timeout_url')->nullable();
            $table->json('raw_callback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('mpesa_b2c_callbacks');
    }
}

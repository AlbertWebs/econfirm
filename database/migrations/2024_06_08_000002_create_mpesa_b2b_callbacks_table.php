<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMpesaB2bCallbacksTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('mpesa_b2b_callbacks', function (Blueprint $table) {
            $table->id();
            $table->string('conversation_id')->nullable();
            $table->string('originator_conversation_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('result_type')->nullable();
            $table->string('result_code')->nullable();
            $table->string('result_desc')->nullable();
            $table->string('command_id')->nullable();
            $table->string('receiver_party_public_name')->nullable();
            $table->string('amount')->nullable();
            $table->string('debit_account_balance')->nullable();
            $table->string('party_a')->nullable();
            $table->string('party_b')->nullable();
            $table->string('transaction_receipt')->nullable();
            $table->string('transaction_completed_datetime')->nullable();
            $table->string('initiator_account_current_balance')->nullable();
            $table->string('charges_paid')->nullable();
            $table->string('currency')->nullable();
            $table->string('receiver_party')->nullable();
            $table->string('transaction_date')->nullable();
            $table->string('b2b_channel')->nullable();
            $table->json('raw_callback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('mpesa_b2b_callbacks');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMpesaB2bTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('mpesa_b2b', function (Blueprint $table) {
            $table->id();
            $table->string('originator_conversation_id')->nullable();
            $table->string('conversation_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('party_a')->nullable();
            $table->string('party_b')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('result_code')->nullable();
            $table->string('result_desc')->nullable();
            $table->string('command_id')->nullable();
            $table->string('initiator')->nullable();
            $table->string('security_credential')->nullable();
            $table->string('remarks')->nullable();
            $table->string('occasion')->nullable();
            $table->string('status')->default('pending');
            $table->json('raw_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('mpesa_b2b');
    }
}

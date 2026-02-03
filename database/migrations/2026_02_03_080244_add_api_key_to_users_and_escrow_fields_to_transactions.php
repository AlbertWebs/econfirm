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
        Schema::table('users', function (Blueprint $table) {
            $table->string('api_key')->nullable()->unique()->after('email');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('buyer_email')->nullable()->after('sender_mobile');
            $table->string('seller_email')->nullable()->after('receiver_mobile');
            $table->string('currency', 3)->default('KES')->after('transaction_amount');
            $table->text('terms')->nullable()->after('transaction_details');
            // Check if otp column exists before using it as reference
            if (Schema::hasColumn('transactions', 'otp')) {
                $table->string('confirmation_code')->nullable()->after('otp');
            } else {
                $table->string('confirmation_code')->nullable()->after('transaction_details');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('api_key');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['buyer_email', 'seller_email', 'currency', 'terms', 'confirmation_code']);
        });
    }
};

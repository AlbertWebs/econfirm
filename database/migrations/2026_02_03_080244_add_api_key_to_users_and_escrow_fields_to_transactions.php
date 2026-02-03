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
            if (!Schema::hasColumn('users', 'api_key')) {
                $table->string('api_key')->nullable()->unique()->after('email');
            }
        });

        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'buyer_email')) {
                $table->string('buyer_email')->nullable()->after('sender_mobile');
            }
            if (!Schema::hasColumn('transactions', 'seller_email')) {
                $table->string('seller_email')->nullable()->after('receiver_mobile');
            }
            if (!Schema::hasColumn('transactions', 'currency')) {
                $table->string('currency', 3)->default('KES')->after('transaction_amount');
            }
            if (!Schema::hasColumn('transactions', 'terms')) {
                $table->text('terms')->nullable()->after('transaction_details');
            }
            if (!Schema::hasColumn('transactions', 'confirmation_code')) {
                // Check if otp column exists before using it as reference
                if (Schema::hasColumn('transactions', 'otp')) {
                    $table->string('confirmation_code')->nullable()->after('otp');
                } else {
                    $table->string('confirmation_code')->nullable()->after('transaction_details');
                }
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mpesa_b2c', function (Blueprint $table) {
            $table->unsignedBigInteger('approved_by_admin_id')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('approved_by_admin_id');
            $table->unsignedBigInteger('rejected_by_admin_id')->nullable()->after('approved_at');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by_admin_id');
            $table->text('rejection_reason')->nullable()->after('rejected_at');
            $table->string('source_transaction_id', 64)->nullable()->after('rejection_reason')
                ->comment('Escrow transaction_id when payout is tied to an escrow record');
        });

        Schema::table('mpesa_b2b', function (Blueprint $table) {
            $table->unsignedBigInteger('approved_by_admin_id')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('approved_by_admin_id');
            $table->unsignedBigInteger('rejected_by_admin_id')->nullable()->after('approved_at');
            $table->timestamp('rejected_at')->nullable()->after('rejected_by_admin_id');
            $table->text('rejection_reason')->nullable()->after('rejected_at');
            $table->string('source_transaction_id', 64)->nullable()->after('rejection_reason')
                ->comment('Escrow transaction_id when transfer is tied to an escrow record');
        });
    }

    public function down(): void
    {
        Schema::table('mpesa_b2c', function (Blueprint $table) {
            $table->dropColumn([
                'approved_by_admin_id',
                'approved_at',
                'rejected_by_admin_id',
                'rejected_at',
                'rejection_reason',
                'source_transaction_id',
            ]);
        });

        Schema::table('mpesa_b2b', function (Blueprint $table) {
            $table->dropColumn([
                'approved_by_admin_id',
                'approved_at',
                'rejected_by_admin_id',
                'rejected_at',
                'rejection_reason',
                'source_transaction_id',
            ]);
        });
    }
};

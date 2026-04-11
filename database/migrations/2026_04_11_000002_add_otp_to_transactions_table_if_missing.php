<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Some production DBs were created without `otp`; approve-transaction / createOTP require it.
     */
    public function up(): void
    {
        if (! Schema::hasTable('transactions')) {
            return;
        }

        if (! Schema::hasColumn('transactions', 'otp')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->string('otp')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transactions') && Schema::hasColumn('transactions', 'otp')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn('otp');
            });
        }
    }
};

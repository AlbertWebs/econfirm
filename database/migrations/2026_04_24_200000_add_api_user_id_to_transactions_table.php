<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('transactions', 'api_user_id')) {
                $table->unsignedBigInteger('api_user_id')->nullable()->index()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'api_user_id')) {
                $table->dropColumn('api_user_id');
            }
        });
    }
};

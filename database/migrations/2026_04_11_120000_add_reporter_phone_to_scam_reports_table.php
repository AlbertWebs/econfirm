<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scam_reports', function (Blueprint $table) {
            $table->string('reporter_phone', 40)->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('scam_reports', function (Blueprint $table) {
            $table->dropColumn('reporter_phone');
        });
    }
};

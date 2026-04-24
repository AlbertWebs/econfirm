<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mpesa_stk_pushes', function (Blueprint $table) {
            $table->string('initiator_ip', 45)->nullable()->after('phone')->index();
        });
    }

    public function down(): void
    {
        Schema::table('mpesa_stk_pushes', function (Blueprint $table) {
            $table->dropIndex(['initiator_ip']);
            $table->dropColumn('initiator_ip');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'notify_email')) {
                $table->boolean('notify_email')->default(true)->after('zip');
            }
            if (! Schema::hasColumn('users', 'notify_sms')) {
                $table->boolean('notify_sms')->default(false)->after('notify_email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'notify_sms')) {
                $table->dropColumn('notify_sms');
            }
            if (Schema::hasColumn('users', 'notify_email')) {
                $table->dropColumn('notify_email');
            }
        });
    }
};

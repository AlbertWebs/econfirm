<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('admins')) {
            return;
        }
        Schema::table('admins', function (Blueprint $table) {
            if (! Schema::hasColumn('admins', 'name')) {
                $table->string('name')->nullable()->after('id');
            }
            if (! Schema::hasColumn('admins', 'email')) {
                $table->string('email')->unique()->nullable()->after('name');
            }
            if (! Schema::hasColumn('admins', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            if (! Schema::hasColumn('admins', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }
            if (! Schema::hasColumn('admins', 'remember_token')) {
                $table->rememberToken();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('admins')) {
            return;
        }
        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
            if (Schema::hasColumn('admins', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
            if (Schema::hasColumn('admins', 'password')) {
                $table->dropColumn('password');
            }
            if (Schema::hasColumn('admins', 'email')) {
                $table->dropIndex(['email']);
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('admins', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};

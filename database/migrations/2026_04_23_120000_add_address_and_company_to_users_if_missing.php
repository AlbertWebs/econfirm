<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Older production `users` tables may lack address/company columns; add if missing.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $columns = [
            'street' => function (Blueprint $table) {
                $table->string('street')->nullable();
            },
            'city' => function (Blueprint $table) {
                $table->string('city')->nullable();
            },
            'state' => function (Blueprint $table) {
                $table->string('state')->nullable();
            },
            'company' => function (Blueprint $table) {
                $table->string('company')->nullable();
            },
            'zip' => function (Blueprint $table) {
                $table->string('zip')->nullable();
            },
        ];

        foreach ($columns as $name => $def) {
            if (! Schema::hasColumn('users', $name)) {
                Schema::table('users', function (Blueprint $table) use ($name, $def) {
                    $def($table);
                });
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        foreach (['zip', 'company', 'state', 'city', 'street'] as $name) {
            if (Schema::hasColumn('users', $name)) {
                Schema::table('users', function (Blueprint $table) use ($name) {
                    $table->dropColumn($name);
                });
            }
        }
    }
};

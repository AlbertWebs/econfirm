<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('phone', 32)->unique();
            $table->unsignedInteger('stk_attempts')->default(0);
            $table->timestamp('last_stk_attempt_at')->nullable();
            $table->timestamp('last_imported_at')->nullable();
            $table->string('source', 64)->default('stk');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_contacts');
    }
};

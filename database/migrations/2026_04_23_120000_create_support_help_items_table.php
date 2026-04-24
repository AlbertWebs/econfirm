<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_help_items', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 32)->index();
            $table->string('ref_key', 64)->nullable()->unique();
            $table->string('title');
            $table->text('body');
            $table->string('icon', 120)->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_published')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_help_items');
    }
};

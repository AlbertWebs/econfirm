<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('recipient', 40);
            $table->string('sender', 60)->nullable();
            $table->string('correlator', 120)->nullable();
            $table->text('message');
            $table->boolean('is_success')->default(false);
            $table->string('provider_message', 255)->nullable();
            $table->string('provider_unique_id', 120)->nullable();
            $table->unsignedSmallInteger('http_code')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->timestamps();

            $table->index(['is_success', 'created_at']);
            $table->index('correlator');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};

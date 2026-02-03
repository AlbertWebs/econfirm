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
        Schema::create('scam_report_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scam_report_id')->constrained('scam_reports')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            // Prevent duplicate likes from same IP (optional - can be removed if you want to allow multiple likes)
            $table->unique(['scam_report_id', 'ip_address'], 'unique_like_per_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scam_report_likes');
    }
};

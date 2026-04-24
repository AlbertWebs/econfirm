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
        Schema::create('scam_report_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scam_report_id')->constrained('scam_reports')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('scam_report_comments')->cascadeOnDelete();
            $table->string('author_name', 80)->nullable();
            $table->string('author_email')->nullable();
            $table->text('body');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();

            $table->index(['scam_report_id', 'parent_id', 'created_at'], 'scam_report_comments_thread_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scam_report_comments');
    }
};

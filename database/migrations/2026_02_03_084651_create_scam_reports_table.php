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
        Schema::create('scam_reports', function (Blueprint $table) {
            $table->id();
            $table->enum('report_type', ['website', 'phone', 'email']);
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('reported_email')->nullable();
            $table->string('category');
            $table->text('description');
            $table->string('email')->nullable();
            $table->date('date_of_incident')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->integer('report_count')->default(1); // Number of times this has been reported
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scam_reports');
    }
};

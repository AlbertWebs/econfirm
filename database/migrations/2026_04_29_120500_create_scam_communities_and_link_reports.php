<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scam_communities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 140)->unique();
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('scam_reports', function (Blueprint $table) {
            if (! Schema::hasColumn('scam_reports', 'community_id')) {
                $table->foreignId('community_id')->nullable()->after('category_other')
                    ->constrained('scam_communities')
                    ->nullOnDelete();
                $table->index('community_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('scam_reports', function (Blueprint $table) {
            if (Schema::hasColumn('scam_reports', 'community_id')) {
                $table->dropConstrainedForeignId('community_id');
            }
        });

        Schema::dropIfExists('scam_communities');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scam_community_admins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scam_community_id')->constrained('scam_communities')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 20)->default('pending'); // pending|approved|rejected
            $table->unsignedBigInteger('approved_by_admin_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->unique(['scam_community_id', 'user_id']);
        });

        Schema::table('scam_reports', function (Blueprint $table) {
            if (! Schema::hasColumn('scam_reports', 'community_moderation_status')) {
                $table->string('community_moderation_status', 20)->nullable()->after('community_id'); // pending|approved|rejected
                $table->unsignedBigInteger('community_moderated_by_user_id')->nullable()->after('community_moderation_status');
                $table->timestamp('community_moderated_at')->nullable()->after('community_moderated_by_user_id');
                $table->index('community_moderation_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('scam_reports', function (Blueprint $table) {
            if (Schema::hasColumn('scam_reports', 'community_moderation_status')) {
                $table->dropColumn(['community_moderation_status', 'community_moderated_by_user_id', 'community_moderated_at']);
            }
        });

        Schema::dropIfExists('scam_community_admins');
    }
};

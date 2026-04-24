<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('live_chat_id')->nullable()->unique()->constrained('live_chats')->nullOnDelete();
            $table->string('status', 32)->default('Created');
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        if (! Schema::hasTable('live_chats')) {
            return;
        }

        $now = now();
        foreach (DB::table('live_chats')->orderBy('id')->cursor() as $row) {
            DB::table('disputes')->insert([
                'transaction_id' => $row->transaction_id,
                'live_chat_id' => $row->id,
                'status' => 'Created',
                'created_at' => $row->created_at ?? $now,
                'updated_at' => $row->updated_at ?? $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};

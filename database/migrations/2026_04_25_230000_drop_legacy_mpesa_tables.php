<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('mpesa_b2c_callbacks');
        Schema::dropIfExists('mpesa_b2b_callbacks');
        Schema::dropIfExists('mpesa_c2b_transactions');
        Schema::dropIfExists('mpesa_stk_pushes');
        Schema::dropIfExists('mpesa_b2c');
        Schema::dropIfExists('mpesa_b2b');
    }

    public function down(): void
    {
        // Legacy M-Pesa tables were intentionally retired.
    }
};

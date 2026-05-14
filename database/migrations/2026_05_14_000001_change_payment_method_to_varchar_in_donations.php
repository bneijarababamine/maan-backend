<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE donations MODIFY payment_method VARCHAR(50) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE donations MODIFY payment_method ENUM('cash','bankily','sadad','masrafi') NOT NULL");
    }
};

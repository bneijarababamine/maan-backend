<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->json('screenshots')->nullable()->after('screenshot_public_id');
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->json('screenshots')->nullable()->after('screenshot_public_id');
        });
    }

    public function down(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->dropColumn('screenshots');
        });
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn('screenshots');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_items', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('unit_value');
        });
    }

    public function down(): void
    {
        Schema::table('activity_items', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};

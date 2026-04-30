<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->enum('payment_type', ['financial', 'in_kind'])->default('financial')->after('activity_date');
            $table->string('payment_method')->nullable()->after('payment_type');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'payment_method']);
        });
    }
};

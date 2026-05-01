<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_beneficiaries', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('notes');
            $table->string('screenshot_url')->nullable()->after('payment_method');
            $table->string('screenshot_public_id')->nullable()->after('screenshot_url');
        });
    }

    public function down(): void
    {
        Schema::table('activity_beneficiaries', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'screenshot_url', 'screenshot_public_id']);
        });
    }
};

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
        Schema::table('orphans', function (Blueprint $table) {
            $table->dropColumn(['guardian_name', 'guardian_phone', 'address']);
        });
    }

    public function down(): void
    {
        Schema::table('orphans', function (Blueprint $table) {
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('address')->nullable();
        });
    }
};

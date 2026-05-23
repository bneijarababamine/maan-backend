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
        // Add FK constraint to existing donation_type_id column, add year
        Schema::table('donations', function (Blueprint $table) {
            if (!Schema::hasColumn('donations', 'donation_type_id')) {
                $table->foreignId('donation_type_id')->nullable()->constrained('donation_types')->nullOnDelete();
            } else {
                $table->foreign('donation_type_id')->references('id')->on('donation_types')->nullOnDelete();
            }
            if (!Schema::hasColumn('donations', 'year')) {
                $table->smallInteger('year')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['donation_type_id']);
            $table->dropColumn(['donation_type_id', 'year']);
        });
    }
};

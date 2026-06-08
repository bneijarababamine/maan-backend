<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patient_medications', function (Blueprint $table) {
            $table->dropColumn('consumed_at');
            $table->date('start_date')->after('quantity')->default(now()->toDateString());
            $table->unsignedSmallInteger('duration_value')->after('start_date')->default(30);
            $table->enum('duration_unit', ['days', 'weeks', 'months'])->after('duration_value')->default('days');
        });
    }

    public function down(): void
    {
        Schema::table('patient_medications', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'duration_value', 'duration_unit']);
            $table->date('consumed_at')->nullable();
        });
    }
};

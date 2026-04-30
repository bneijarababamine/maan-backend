<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->unsignedInteger('receipt_number')->nullable()->after('id');
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->unsignedInteger('receipt_number')->nullable()->after('id');
        });

        // Backfill existing contributions ordered by creation date
        $contributions = DB::table('contributions')->orderBy('created_at')->get(['id']);
        foreach ($contributions as $i => $c) {
            DB::table('contributions')->where('id', $c->id)->update(['receipt_number' => $i + 1]);
        }

        // Backfill existing donations ordered by creation date
        $donations = DB::table('donations')->orderBy('created_at')->get(['id']);
        foreach ($donations as $i => $d) {
            DB::table('donations')->where('id', $d->id)->update(['receipt_number' => $i + 1]);
        }
    }

    public function down(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->dropColumn('receipt_number');
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn('receipt_number');
        });
    }
};

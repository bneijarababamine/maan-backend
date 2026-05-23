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
        Schema::table('donations', function (Blueprint $table) {
            // Make donor_id nullable (members can also donate)
            $table->foreignId('donor_id')->nullable()->change();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete()->after('donor_id');
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropColumn('member_id');
            $table->foreignId('donor_id')->nullable(false)->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orphans', function (Blueprint $table) {
            // Ajouter la colonne guardian_id avant de supprimer guardian_name
            $table->foreignId('guardian_id')->nullable()->constrained('guardians')->cascadeOnDelete()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('orphans', function (Blueprint $table) {
            $table->dropForeignKeyIfExists('orphans_guardian_id_foreign');
            $table->dropColumn('guardian_id');
        });
    }
};

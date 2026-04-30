<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orphan_siblings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orphan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sibling_id')->constrained('orphans')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['orphan_id', 'sibling_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orphan_siblings');
    }
};

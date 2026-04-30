<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contribution_months', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contribution_id')->constrained()->cascadeOnDelete();
            $table->integer('year');
            $table->integer('month');
            $table->timestamps();
            $table->unique(['contribution_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contribution_months');
    }
};

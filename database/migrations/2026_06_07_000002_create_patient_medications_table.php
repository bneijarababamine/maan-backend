<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('chronic_patients')->cascadeOnDelete();
            $table->string('name');
            $table->string('image_url')->nullable();
            $table->string('image_public_id')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('quantity', 8, 2)->default(1);
            $table->string('payment_method')->nullable();
            $table->date('consumed_at');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_medications');
    }
};

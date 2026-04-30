<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orphans', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female']);
            $table->string('school_name')->nullable();
            $table->string('grade')->nullable();
            $table->string('guardian_name');
            $table->string('guardian_phone');
            $table->string('address');
            $table->string('photo_url')->nullable();
            $table->string('photo_public_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('deactivated_reason', ['aged_out', 'manual', 'other'])->nullable();
            $table->timestamp('deactivated_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orphans');
    }
};

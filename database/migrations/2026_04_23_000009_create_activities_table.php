<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title_ar');
            $table->string('title_fr');
            $table->text('description_ar')->nullable();
            $table->text('description_fr')->nullable();
            $table->enum('activity_type', [
                'school_fees', 'eid_help', 'food_basket',
                'winter_clothes', 'ramadan', 'other'
            ]);
            $table->enum('beneficiary_type', ['orphans', 'families', 'general']);
            $table->date('activity_date');
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};

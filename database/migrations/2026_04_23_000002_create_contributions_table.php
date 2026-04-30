<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->integer('months_count');
            $table->decimal('amount_per_month', 10, 2)->default(200);
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_method', ['cash', 'bankily', 'sadad', 'masrafi']);
            $table->string('transaction_ref')->nullable();
            $table->string('screenshot_url')->nullable();
            $table->string('screenshot_public_id')->nullable();
            $table->foreignId('registered_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamp('paid_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contributions');
    }
};

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
        Schema::create('revenue_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revenue_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->constrained()->restrictOnDelete();
            $table->foreignId('teacher_id')->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('consumption_seconds');
            $table->unsignedBigInteger('total_consumption_seconds');
            $table->decimal('allocated_amount', 12, 2);
            $table->string('currency', 3)->default('EGP');
            $table->timestamps();

            $table->unique(
                ['subscription_id', 'teacher_id', 'revenue_period_id'],
                'revenue_allocations_unique'
            );
            $table->index(['teacher_id', 'revenue_period_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenue_allocations');
    }
};

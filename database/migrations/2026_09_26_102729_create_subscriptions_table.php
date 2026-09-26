<?php

use App\Enums\SubscriptionStatus;
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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_option_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('status')
                ->default(SubscriptionStatus::Pending->value)
                ->comment(SubscriptionStatus::class);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('EGP');
            $table->decimal('platform_percentage', 5, 2);
            $table->decimal('platform_amount', 12, 2);
            $table->decimal('teacher_pool_amount', 12, 2);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['student_id', 'status']);
            $table->index(['starts_at', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

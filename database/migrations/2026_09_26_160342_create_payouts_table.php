<?php

use App\Enums\PayoutStatus;
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
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('EGP');
            $table->unsignedTinyInteger('status')
                ->default(PayoutStatus::Pending->value)
                ->comment(PayoutStatus::class);
            $table->text('note')->nullable();
            $table->timestamp('requested_at');
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('processed_by_manager_id')
                ->nullable()
                ->constrained('managers')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['teacher_id', 'status']);
            $table->index('status');
            $table->index('requested_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};

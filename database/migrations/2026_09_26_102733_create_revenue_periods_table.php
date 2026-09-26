<?php

use App\Enums\RevenuePeriodStatus;
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
        Schema::create('revenue_periods', function (Blueprint $table) {
            $table->id();
            $table->date('period_start');
            $table->date('period_end');
            $table->unsignedTinyInteger('status')
                ->default(RevenuePeriodStatus::Open->value)
                ->comment(RevenuePeriodStatus::class);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->unique(['period_start', 'period_end']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenue_periods');
    }
};

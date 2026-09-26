<?php

namespace Database\Factories;

use App\Enums\RevenuePeriodStatus;
use App\Models\RevenuePeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RevenuePeriod>
 */
class RevenuePeriodFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = now()->startOfMonth();

        return [
            'period_start' => $start->toDateString(),
            'period_end' => $start->copy()->endOfMonth()->toDateString(),
            'status' => RevenuePeriodStatus::Open,
            'processed_at' => null,
        ];
    }
}

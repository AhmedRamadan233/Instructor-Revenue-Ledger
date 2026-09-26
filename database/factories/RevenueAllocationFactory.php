<?php

namespace Database\Factories;

use App\Models\RevenueAllocation;
use App\Models\RevenuePeriod;
use App\Models\Subscription;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RevenueAllocation>
 */
class RevenueAllocationFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'revenue_period_id' => RevenuePeriod::factory(),
            'subscription_id' => Subscription::factory(),
            'teacher_id' => Teacher::factory(),
            'consumption_seconds' => 36000,
            'total_consumption_seconds' => 72000,
            'allocated_amount' => 400.00,
            'currency' => 'EGP',
        ];
    }
}

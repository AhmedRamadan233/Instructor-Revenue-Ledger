<?php

namespace Database\Factories;

use App\Enums\SubscriptionStatus;
use App\Models\PlanOption;
use App\Models\Student;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = 1000.00;
        $platformPercentage = 20.00;
        $platformAmount = $amount * ($platformPercentage / 100);

        return [
            'student_id' => Student::factory(),
            'plan_option_id' => PlanOption::factory(),
            'status' => SubscriptionStatus::Active,
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
            'amount' => $amount,
            'currency' => 'EGP',
            'platform_percentage' => $platformPercentage,
            'platform_amount' => $platformAmount,
            'teacher_pool_amount' => $amount - $platformAmount,
            'pool_carry_amount' => 0,
            'paid_at' => now(),
        ];
    }
}

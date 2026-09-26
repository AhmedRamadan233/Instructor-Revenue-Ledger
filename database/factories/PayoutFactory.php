<?php

namespace Database\Factories;

use App\Enums\PayoutStatus;
use App\Models\Payout;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payout>
 */
class PayoutFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'teacher_id' => Teacher::factory(),
            'amount' => fake()->randomFloat(2, 50, 500),
            'currency' => 'EGP',
            'status' => PayoutStatus::Pending,
            'note' => null,
            'requested_at' => now(),
            'processed_at' => null,
            'processed_by_manager_id' => null,
        ];
    }
}

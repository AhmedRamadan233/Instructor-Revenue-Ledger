<?php

namespace Database\Factories;

use App\Enums\PlanType;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(PlanType::cases());

        return [
            'name' => $type->label().' Plan',
            'type' => $type,
            'price' => fake()->randomFloat(2, 100, 2000),
            'currency' => 'EGP',
            'duration_months' => $type->durationMonths(),
            'is_active' => true,
        ];
    }
}

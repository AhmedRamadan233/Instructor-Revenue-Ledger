<?php

namespace Database\Seeders;

use App\Enums\PlanType;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Monthly Plan',
                'type' => PlanType::Monthly,
                'price' => 100.00,
                'duration_months' => PlanType::Monthly->durationMonths(),
            ],
            [
                'name' => 'Quarterly Plan',
                'type' => PlanType::Quarterly,
                'price' => 270.00,
                'duration_months' => PlanType::Quarterly->durationMonths(),
            ],
            [
                'name' => 'Half Yearly Plan',
                'type' => PlanType::HalfYearly,
                'price' => 500.00,
                'duration_months' => PlanType::HalfYearly->durationMonths(),
            ],
            [
                'name' => 'Yearly Plan',
                'type' => PlanType::Yearly,
                'price' => 900.00,
                'duration_months' => PlanType::Yearly->durationMonths(),
            ],
        ];

        foreach ($plans as $plan) {
            Plan::query()->create([
                ...$plan,
                'currency' => 'EGP',
                'is_active' => true,
            ]);
        }
    }
}

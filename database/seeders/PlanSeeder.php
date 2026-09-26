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
        $catalog = [
            [
                'name' => 'Standard Access',
                'description' => 'Access all published courses from every teacher on the platform.',
                'options' => [
                    PlanType::Monthly->value => 100.00,
                    PlanType::Quarterly->value => 270.00,
                    PlanType::HalfYearly->value => 500.00,
                    PlanType::Yearly->value => 900.00,
                ],
            ],
            [
                'name' => 'Premium Access',
                'description' => 'Everything in Standard, with priority support for students.',
                'options' => [
                    PlanType::Monthly->value => 150.00,
                    PlanType::Quarterly->value => 400.00,
                    PlanType::HalfYearly->value => 750.00,
                    PlanType::Yearly->value => 1300.00,
                ],
            ],
        ];

        foreach ($catalog as $item) {
            $plan = Plan::query()->create([
                'name' => $item['name'],
                'description' => $item['description'],
                'is_active' => true,
            ]);

            foreach ($item['options'] as $typeValue => $price) {
                $type = PlanType::from($typeValue);

                $plan->options()->create([
                    'type' => $type,
                    'price' => $price,
                    'currency' => 'EGP',
                    'duration_months' => $type->durationMonths(),
                    'is_active' => true,
                ]);
            }
        }
    }
}

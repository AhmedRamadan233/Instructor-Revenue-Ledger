<?php

namespace Database\Factories;

use App\Enums\SubscriptionPaymentStatus;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubscriptionPayment>
 */
class SubscriptionPaymentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subscription_id' => Subscription::factory(),
            'amount' => 1000.00,
            'currency' => 'EGP',
            'status' => SubscriptionPaymentStatus::Paid,
            'provider' => 'manual',
            'provider_reference' => fake()->uuid(),
            'paid_at' => now(),
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Enums\SubscriptionPaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\PlanOption;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $student = Student::query()->withoutGlobalScopes()->first();
        $planOption = PlanOption::query()->orderBy('price')->first();

        if ($student === null || $planOption === null) {
            return;
        }

        $platformPercentage = (float) (Setting::query()->withoutGlobalScopes()->where('key', 'platform_revenue_percentage')->value('value') ?? 20);
        $amount = (float) $planOption->price;
        $platformAmount = round($amount * ($platformPercentage / 100), 2);

        $subscription = Subscription::query()->create([
            'student_id' => $student->id,
            'plan_option_id' => $planOption->id,
            'status' => SubscriptionStatus::Active,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonths($planOption->duration_months),
            'amount' => $amount,
            'currency' => $planOption->currency,
            'platform_percentage' => $platformPercentage,
            'platform_amount' => $platformAmount,
            'teacher_pool_amount' => $amount - $platformAmount,
            'paid_at' => now()->subDay(),
        ]);

        SubscriptionPayment::query()->create([
            'subscription_id' => $subscription->id,
            'amount' => $amount,
            'currency' => $planOption->currency,
            'status' => SubscriptionPaymentStatus::Paid,
            'provider' => 'manual',
            'provider_reference' => 'seed-payment-1',
            'paid_at' => now()->subDay(),
        ]);
    }
}

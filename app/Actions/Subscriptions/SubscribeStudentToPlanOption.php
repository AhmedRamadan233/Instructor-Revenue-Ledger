<?php

namespace App\Actions\Subscriptions;

use App\Enums\SubscriptionPaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\PlanOption;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubscribeStudentToPlanOption
{
    public function handle(Student $student, PlanOption $planOption): Subscription
    {
        if (! $planOption->is_active || ! $planOption->plan?->is_active) {
            throw ValidationException::withMessages([
                'planOptionId' => 'This plan option is not available.',
            ]);
        }

        $hasActive = Subscription::query()
            ->withoutGlobalScopes()
            ->where('student_id', $student->id)
            ->where('status', SubscriptionStatus::Active)
            ->where(function ($query): void {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            })
            ->exists();

        if ($hasActive) {
            throw ValidationException::withMessages([
                'planOptionId' => 'You already have an active subscription. Cancel it before subscribing again.',
            ]);
        }

        $platformPercentage = (float) (Setting::query()
            ->withoutGlobalScopes()
            ->where('key', 'platform_revenue_percentage')
            ->value('value') ?? 20);

        $amount = round((float) $planOption->price, 2);
        $platformAmount = round($amount * ($platformPercentage / 100), 2);
        $teacherPoolAmount = round($amount - $platformAmount, 2);

        return DB::transaction(function () use (
            $student,
            $planOption,
            $amount,
            $platformPercentage,
            $platformAmount,
            $teacherPoolAmount,
        ): Subscription {
            $subscription = Subscription::query()->create([
                'student_id' => $student->id,
                'plan_option_id' => $planOption->id,
                'status' => SubscriptionStatus::Active,
                'starts_at' => now(),
                'ends_at' => now()->addMonths($planOption->duration_months),
                'amount' => $amount,
                'currency' => $planOption->currency,
                'platform_percentage' => $platformPercentage,
                'platform_amount' => $platformAmount,
                'teacher_pool_amount' => $teacherPoolAmount,
                'pool_carry_amount' => 0,
                'paid_at' => now(),
            ]);

            SubscriptionPayment::query()->create([
                'subscription_id' => $subscription->id,
                'amount' => $amount,
                'currency' => $planOption->currency,
                'status' => SubscriptionPaymentStatus::Paid,
                'provider' => 'manual',
                'provider_reference' => 'manual-'.uniqid(),
                'paid_at' => now(),
            ]);

            return $subscription;
        });
    }
}

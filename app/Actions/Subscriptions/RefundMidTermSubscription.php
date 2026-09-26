<?php

namespace App\Actions\Subscriptions;

use App\Enums\RevenuePeriodStatus;
use App\Enums\SubscriptionPaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use App\Repo\InterFace\CourseConsumptionSessionRepositoryInterface;
use App\Repo\InterFace\RevenuePeriodRepositoryInterface;
use App\Repo\InterFace\SubscriptionPaymentRepositoryInterface;
use App\Repo\InterFace\SubscriptionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Mid-term refund for a prepaid subscription.
 *
 * Decision (documented in docs/ARCHITECTURE.md):
 * - Money already settled in Processed revenue periods is retained (platform + teachers).
 * - Remaining unused months of the prepaid term are refunded to the student record.
 * - No teacher ledger clawback for processed months; clear pool_carry so future closes
 *   do not keep allocating this subscription's unused pool.
 */
class RefundMidTermSubscription
{
    public function __construct(
        private SubscriptionRepositoryInterface $subscriptions,
        private SubscriptionPaymentRepositoryInterface $payments,
        private CourseConsumptionSessionRepositoryInterface $sessions,
        private RevenuePeriodRepositoryInterface $periods,
    ) {}

    /**
     * @return array{
     *     subscription: Subscription,
     *     refund_amount: float,
     *     retained_amount: float,
     *     duration_months: int,
     *     processed_months: int,
     *     refundable_months: int
     * }
     */
    public function handle(Subscription $subscription): array
    {
        if ($subscription->status !== SubscriptionStatus::Active) {
            throw ValidationException::withMessages([
                'subscription' => 'Only active subscriptions can be refunded mid-term.',
            ]);
        }

        $subscription->loadMissing('planOption');

        $durationMonths = max(1, (int) ($subscription->planOption?->duration_months ?? 1));
        $processedMonths = $this->countProcessedMonthsOverlapping($subscription);
        $refundableMonths = max(0, $durationMonths - $processedMonths);

        $refundAmount = round(((float) $subscription->amount) * ($refundableMonths / $durationMonths), 2);
        $retainedAmount = round(((float) $subscription->amount) - $refundAmount, 2);

        return DB::transaction(function () use (
            $subscription,
            $refundAmount,
            $retainedAmount,
            $durationMonths,
            $processedMonths,
            $refundableMonths,
        ): array {
            $this->subscriptions->update($subscription->id, [
                'status' => SubscriptionStatus::Refunded,
                'ends_at' => now(),
                'pool_carry_amount' => 0,
            ], withoutGlobalScopes: true);

            $this->sessions->query(true)
                ->where('subscription_id', $subscription->id)
                ->whereNull('ended_at')
                ->update([
                    'ended_at' => now(),
                    'last_activity_at' => now(),
                ]);

            $this->payments->updateWhere(
                [
                    'subscription_id' => $subscription->id,
                    'status' => SubscriptionPaymentStatus::Paid,
                ],
                ['status' => SubscriptionPaymentStatus::Refunded],
                withoutGlobalScopes: true,
            );

            if ($refundAmount > 0) {
                $this->payments->create([
                    'subscription_id' => $subscription->id,
                    'amount' => $refundAmount,
                    'currency' => $subscription->currency,
                    'status' => SubscriptionPaymentStatus::Refunded,
                    'provider' => 'manual_refund',
                    'provider_reference' => 'refund-'.$subscription->id.'-'.uniqid(),
                    'paid_at' => now(),
                ]);
            }

            return [
                'subscription' => $subscription->refresh(),
                'refund_amount' => $refundAmount,
                'retained_amount' => $retainedAmount,
                'duration_months' => $durationMonths,
                'processed_months' => $processedMonths,
                'refundable_months' => $refundableMonths,
            ];
        });
    }

    protected function countProcessedMonthsOverlapping(Subscription $subscription): int
    {
        $windowStart = $subscription->starts_at?->copy()->startOfDay() ?? now()->startOfDay();
        $windowEnd = now()->endOfDay();

        return $this->periods->query(true)
            ->where('status', RevenuePeriodStatus::Processed)
            ->whereDate('period_start', '<=', $windowEnd->toDateString())
            ->whereDate('period_end', '>=', $windowStart->toDateString())
            ->count();
    }
}

<?php

namespace App\Actions\Revenue;

use App\Enums\LedgerEntryType;
use App\Enums\RevenuePeriodStatus;
use App\Enums\SubscriptionStatus;
use App\Models\CourseConsumptionSession;
use App\Models\RevenuePeriod;
use App\Models\Subscription;
use App\Repo\InterFace\CourseConsumptionSessionRepositoryInterface;
use App\Repo\InterFace\RevenueAllocationRepositoryInterface;
use App\Repo\InterFace\RevenuePeriodRepositoryInterface;
use App\Repo\InterFace\SubscriptionRepositoryInterface;
use App\Repo\InterFace\TeacherLedgerEntryRepositoryInterface;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProcessRevenuePeriod
{
    public function __construct(
        private RevenuePeriodRepositoryInterface $periods,
        private RevenueAllocationRepositoryInterface $allocations,
        private TeacherLedgerEntryRepositoryInterface $ledgerEntries,
        private SubscriptionRepositoryInterface $subscriptions,
        private CourseConsumptionSessionRepositoryInterface $sessions,
    ) {}

    /**
     * @return array{
     *     period: RevenuePeriod,
     *     allocations: int,
     *     ledger_entries: int,
     *     carried_forward: int,
     *     subscriptions_considered: int
     * }
     */
    public function handle(
        CarbonInterface $periodStart,
        CarbonInterface $periodEnd,
        bool $force = false,
    ): array {
        $periodStart = $periodStart->copy()->startOfDay();
        $periodEnd = $periodEnd->copy()->endOfDay();

        $this->assertCloseableCalendarMonth($periodStart, $periodEnd, $force);

        return DB::transaction(function () use ($periodStart, $periodEnd, $force): array {
            $period = $this->periods->query(true)
                ->whereDate('period_start', $periodStart->toDateString())
                ->whereDate('period_end', $periodEnd->toDateString())
                ->first();

            if ($period === null) {
                $period = $this->periods->create([
                    'period_start' => $periodStart->toDateString(),
                    'period_end' => $periodEnd->toDateString(),
                    'status' => RevenuePeriodStatus::Open,
                ]);
            }

            if ($period->status === RevenuePeriodStatus::Processed && ! $force) {
                $label = $periodStart->format('F Y');
                $range = sprintf(
                    '%s → %s',
                    $periodStart->toDateString(),
                    $periodEnd->toDateString(),
                );
                $processedAt = optional($period->processed_at)->format('Y-m-d H:i') ?? 'unknown time';

                throw ValidationException::withMessages([
                    'period' => sprintf(
                        'Cannot process %s (%s): this period is already locked as Processed (at %s). Re-running would duplicate earnings unless you use the demo re-run button, which rebuilds allocations for this month only.',
                        $label,
                        $range,
                        $processedAt,
                    ),
                ]);
            }

            if ($force && $period->status === RevenuePeriodStatus::Processed) {
                $this->periods->update($period->id, [
                    'status' => RevenuePeriodStatus::Open,
                    'processed_at' => null,
                ], withoutGlobalScopes: true);

                $period->refresh();
            }

            $this->allocations->deleteWhere(
                ['revenue_period_id' => $period->id],
                withoutGlobalScopes: true,
            );

            $this->ledgerEntries->deleteWhere(
                [
                    'revenue_period_id' => $period->id,
                    'type' => LedgerEntryType::Earning,
                ],
                withoutGlobalScopes: true,
            );

            $allocationsCount = 0;
            $carriedForward = 0;
            $teacherTotals = [];

            $subscriptions = $this->subscriptions->query(true)
                ->whereIn('status', [
                    SubscriptionStatus::Active,
                    SubscriptionStatus::Expired,
                    SubscriptionStatus::Cancelled,
                    SubscriptionStatus::Refunded,
                ])
                ->where('starts_at', '<=', $periodEnd)
                ->where(function ($query) use ($periodStart): void {
                    $query->whereNull('ends_at')
                        ->orWhere('ends_at', '>=', $periodStart);
                })
                ->with('planOption')
                ->lockForUpdate()
                ->get();

            foreach ($subscriptions as $subscription) {
                $result = $this->allocateSubscription(
                    $period,
                    $subscription,
                    $periodStart,
                    $periodEnd,
                    $teacherTotals,
                );

                $allocationsCount += $result['allocations'];
                $carriedForward += $result['carried'] ? 1 : 0;
            }

            $ledgerEntries = 0;

            foreach ($teacherTotals as $teacherId => $data) {
                $this->ledgerEntries->create([
                    'teacher_id' => $teacherId,
                    'type' => LedgerEntryType::Earning,
                    'amount' => $data['amount'],
                    'currency' => $data['currency'],
                    'reference_type' => RevenuePeriod::class,
                    'reference_id' => $period->id,
                    'revenue_period_id' => $period->id,
                ]);
                $ledgerEntries++;
            }

            $this->periods->update($period->id, [
                'status' => RevenuePeriodStatus::Processed,
                'processed_at' => now(),
            ], withoutGlobalScopes: true);

            return [
                'period' => $period->refresh(),
                'allocations' => $allocationsCount,
                'ledger_entries' => $ledgerEntries,
                'carried_forward' => $carriedForward,
                'subscriptions_considered' => $subscriptions->count(),
            ];
        });
    }

    protected function assertCloseableCalendarMonth(
        CarbonInterface $periodStart,
        CarbonInterface $periodEnd,
        bool $force = false,
    ): void {
        if ($periodEnd->lt($periodStart)) {
            throw ValidationException::withMessages([
                'period' => sprintf(
                    'Invalid range: end date (%s) is before start date (%s).',
                    $periodEnd->toDateString(),
                    $periodStart->toDateString(),
                ),
            ]);
        }

        $expectedStart = $periodStart->copy()->startOfMonth()->startOfDay();
        $expectedEnd = $periodStart->copy()->endOfMonth()->endOfDay();

        if (
            $periodStart->toDateString() !== $expectedStart->toDateString()
            || $periodEnd->toDateString() !== $expectedEnd->toDateString()
            || ! $periodStart->isSameMonth($periodEnd)
        ) {
            throw ValidationException::withMessages([
                'period' => sprintf(
                    'Revenue periods must be a full calendar month (expected %s → %s, got %s → %s).',
                    $expectedStart->toDateString(),
                    $expectedEnd->toDateString(),
                    $periodStart->toDateString(),
                    $periodEnd->toDateString(),
                ),
            ]);
        }

        if ($force) {
            return;
        }

        $latestCloseable = now()->copy()->subMonthNoOverflow();
        $requested = $periodStart->copy()->startOfMonth();

        if ($requested->format('Y-m') > $latestCloseable->format('Y-m')) {
            throw ValidationException::withMessages([
                'period' => sprintf(
                    'Cannot process %s yet: only finished months can be closed. Latest allowed month is %s (today is %s). Wait until this month ends, or use the demo re-run button for a test pass.',
                    $requested->format('F Y'),
                    $latestCloseable->format('F Y'),
                    now()->toDateString(),
                ),
            ]);
        }
    }

    /**
     * @param  array<int, array{amount: float, currency: string}>  $teacherTotals
     * @return array{allocations: int, carried: bool}
     */
    protected function allocateSubscription(
        RevenuePeriod $period,
        Subscription $subscription,
        CarbonInterface $periodStart,
        CarbonInterface $periodEnd,
        array &$teacherTotals,
    ): array {
        $durationMonths = max(1, (int) ($subscription->planOption?->duration_months ?? 1));
        $monthlyShare = round(((float) $subscription->teacher_pool_amount) / $durationMonths, 2);
        $periodPool = round($monthlyShare + (float) $subscription->pool_carry_amount, 2);

        if ($periodPool <= 0) {
            return ['allocations' => 0, 'carried' => false];
        }

        $sessions = $this->sessions->query(true)
            ->with(['course' => fn ($query) => $query->withoutGlobalScopes()])
            ->where('subscription_id', $subscription->id)
            ->whereBetween('started_at', [$periodStart, $periodEnd])
            ->get()
            ->filter(fn (CourseConsumptionSession $session): bool => $session->course !== null);

        $consumptionByTeacher = $sessions
            ->groupBy(fn (CourseConsumptionSession $session): int => (int) $session->course->teacher_id)
            ->map(fn ($group): int => (int) $group->sum('watch_seconds'));

        $totalSeconds = (int) $consumptionByTeacher->sum();

        if ($totalSeconds <= 0) {
            $this->subscriptions->update($subscription->id, [
                'pool_carry_amount' => $periodPool,
            ], withoutGlobalScopes: true);

            return ['allocations' => 0, 'carried' => true];
        }

        $created = 0;
        $allocatedSum = 0.0;
        $teachers = $consumptionByTeacher->keys()->values();
        $lastIndex = $teachers->count() - 1;

        foreach ($teachers as $index => $teacherId) {
            $seconds = (int) $consumptionByTeacher[$teacherId];

            if ($index === $lastIndex) {
                $amount = round($periodPool - $allocatedSum, 2);
            } else {
                $amount = round($periodPool * ($seconds / $totalSeconds), 2);
                $allocatedSum += $amount;
            }

            if ($amount <= 0) {
                continue;
            }

            $this->allocations->create([
                'revenue_period_id' => $period->id,
                'subscription_id' => $subscription->id,
                'teacher_id' => $teacherId,
                'consumption_seconds' => $seconds,
                'total_consumption_seconds' => $totalSeconds,
                'allocated_amount' => $amount,
                'currency' => $subscription->currency,
            ]);

            $teacherId = (int) $teacherId;
            $teacherTotals[$teacherId] ??= [
                'amount' => 0.0,
                'currency' => $subscription->currency,
            ];
            $teacherTotals[$teacherId]['amount'] = round(
                $teacherTotals[$teacherId]['amount'] + $amount,
                2
            );

            $created++;
        }

        $this->subscriptions->update($subscription->id, [
            'pool_carry_amount' => 0,
        ], withoutGlobalScopes: true);

        return ['allocations' => $created, 'carried' => false];
    }
}

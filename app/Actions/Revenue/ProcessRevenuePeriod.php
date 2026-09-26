<?php

namespace App\Actions\Revenue;

use App\Enums\LedgerEntryType;
use App\Enums\RevenuePeriodStatus;
use App\Enums\SubscriptionStatus;
use App\Models\CourseConsumptionSession;
use App\Models\RevenueAllocation;
use App\Models\RevenuePeriod;
use App\Models\Subscription;
use App\Models\TeacherLedgerEntry;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProcessRevenuePeriod
{
    public function handle(CarbonInterface $periodStart, CarbonInterface $periodEnd): array
    {
        $periodStart = $periodStart->copy()->startOfDay();
        $periodEnd = $periodEnd->copy()->endOfDay();

        $this->assertCloseableCalendarMonth($periodStart, $periodEnd);

        return DB::transaction(function () use ($periodStart, $periodEnd): array {
            $period = RevenuePeriod::query()
                ->withoutGlobalScopes()
                ->whereDate('period_start', $periodStart->toDateString())
                ->whereDate('period_end', $periodEnd->toDateString())
                ->first();

            if ($period === null) {
                $period = RevenuePeriod::query()->withoutGlobalScopes()->create([
                    'period_start' => $periodStart->toDateString(),
                    'period_end' => $periodEnd->toDateString(),
                    'status' => RevenuePeriodStatus::Open,
                ]);
            }

            if ($period->status === RevenuePeriodStatus::Processed) {
                throw ValidationException::withMessages([
                    'period' => 'This revenue period has already been processed.',
                ]);
            }

            RevenueAllocation::query()
                ->withoutGlobalScopes()
                ->where('revenue_period_id', $period->id)
                ->delete();

            TeacherLedgerEntry::query()
                ->withoutGlobalScopes()
                ->where('revenue_period_id', $period->id)
                ->where('type', LedgerEntryType::Earning)
                ->delete();

            $allocationsCount = 0;
            $carriedForward = 0;
            $teacherTotals = [];

            $subscriptions = Subscription::query()
                ->withoutGlobalScopes()
                ->whereIn('status', [SubscriptionStatus::Active, SubscriptionStatus::Expired, SubscriptionStatus::Cancelled])
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
                TeacherLedgerEntry::query()->create([
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

            $period->update([
                'status' => RevenuePeriodStatus::Processed,
                'processed_at' => now(),
            ]);

            return [
                'period' => $period->refresh(),
                'allocations' => $allocationsCount,
                'ledger_entries' => $ledgerEntries,
                'carried_forward' => $carriedForward,
            ];
        });
    }

    protected function assertCloseableCalendarMonth(
        CarbonInterface $periodStart,
        CarbonInterface $periodEnd,
    ): void {
        if ($periodEnd->lt($periodStart)) {
            throw ValidationException::withMessages([
                'period' => 'Period end must be after period start.',
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
                'period' => 'Revenue periods must cover a full calendar month.',
            ]);
        }

        $latestCloseable = now()->copy()->subMonthNoOverflow()->format('Y-m');

        if ($periodStart->format('Y-m') > $latestCloseable) {
            throw ValidationException::withMessages([
                'period' => 'Only fully completed months can be processed. Wait until the month ends.',
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

        $sessions = CourseConsumptionSession::query()
            ->withoutGlobalScopes()
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
            $subscription->update([
                'pool_carry_amount' => $periodPool,
            ]);

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

            RevenueAllocation::query()->create([
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

        $subscription->update([
            'pool_carry_amount' => 0,
        ]);

        return ['allocations' => $created, 'carried' => false];
    }
}

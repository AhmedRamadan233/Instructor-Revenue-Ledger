<?php

namespace App\Livewire\Dashboard;

use App\Actions\Revenue\ProcessRevenuePeriod;
use App\Livewire\Requests\Dashboard\ProcessRevenueRequest;
use App\Repo\InterFace\CourseConsumptionSessionRepositoryInterface;
use App\Repo\InterFace\RevenueAllocationRepositoryInterface;
use App\Repo\InterFace\RevenuePeriodRepositoryInterface;
use App\Repo\InterFace\TeacherLedgerEntryRepositoryInterface;
use Carbon\Carbon;
use Livewire\Attributes\Title;

#[Title('Revenue')]
class Revenue extends __AbstractManagerComponent
{
    private RevenuePeriodRepositoryInterface $periods;

    private RevenueAllocationRepositoryInterface $allocations;

    private TeacherLedgerEntryRepositoryInterface $ledgerEntries;

    private CourseConsumptionSessionRepositoryInterface $sessions;

    public int $year;

    public int $month;

    public function boot(
        RevenuePeriodRepositoryInterface $periods,
        RevenueAllocationRepositoryInterface $allocations,
        TeacherLedgerEntryRepositoryInterface $ledgerEntries,
        CourseConsumptionSessionRepositoryInterface $sessions,
    ): void {
        $this->periods = $periods;
        $this->allocations = $allocations;
        $this->ledgerEntries = $ledgerEntries;
        $this->sessions = $sessions;
    }

    public function mount(): void
    {
        $previous = now()->subMonthNoOverflow();
        $this->year = $previous->year;
        $this->month = $previous->month;
    }

    public function process(ProcessRevenuePeriod $processor): void
    {
        $this->runProcess($processor, force: false);
    }

    /**
     * Demo / video mode: ignore the Year/Month fields, pick the month that
     * actually has watch time, then run the real allocation + ledger logic.
     */
    public function processForDemo(ProcessRevenuePeriod $processor): void
    {
        $demoMonth = $this->resolveMonthWithMostWatchTime();

        if ($demoMonth === null) {
            session()->flash(
                'error',
                'Demo cannot run yet: there are no course watch sessions. Log in as a student, watch courses for a bit, then click Process (demo / re-run) again. The demo uses real watch ratios — it does not invent earnings.',
            );

            return;
        }

        $this->year = (int) $demoMonth->year;
        $this->month = (int) $demoMonth->month;

        $this->runProcess($processor, force: true, autoPickedForDemo: true);
    }

    protected function runProcess(
        ProcessRevenuePeriod $processor,
        bool $force,
        bool $autoPickedForDemo = false,
    ): void {
        $this->validate(ProcessRevenueRequest::rules());

        $periodStart = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        $result = $processor->handle($periodStart, $periodEnd, force: $force);

        $label = $periodStart->format('F Y');
        $prefix = $force
            ? ($autoPickedForDemo ? '[Demo] Auto-selected '.$label.'. ' : '[Demo re-run] ')
            : '';

        if ($result['allocations'] === 0 && $result['ledger_entries'] === 0) {
            if ($result['subscriptions_considered'] === 0) {
                session()->flash(
                    'error',
                    sprintf(
                        '%s%s closed with 0 allocations: no subscription overlapped this month. Watches only count when their subscription also overlaps the same month.',
                        $prefix,
                        $label,
                    ),
                );

                return;
            }

            if ($result['carried_forward'] > 0) {
                session()->flash(
                    'success',
                    sprintf(
                        '%s%s closed: %d subscription(s) had pool money but 0 watch seconds in this month, so the pool was carried forward. Teachers stay at 0 until a month with watching is processed.',
                        $prefix,
                        $label,
                        $result['carried_forward'],
                    ),
                );

                return;
            }

            session()->flash(
                'error',
                sprintf(
                    '%s%s closed with 0 allocations and 0 carry: overlapping subscriptions had a 0 teacher pool for this month.',
                    $prefix,
                    $label,
                ),
            );

            return;
        }

        session()->flash(
            'success',
            sprintf(
                '%sFull money path applied for %s: %d allocations, %d teacher ledger earnings, %d subscriptions carried forward. Open Reports / teacher Payouts — balances should now show these earnings.',
                $prefix,
                $label,
                $result['allocations'],
                $result['ledger_entries'],
                $result['carried_forward'],
            ),
        );
    }

    protected function resolveMonthWithMostWatchTime(): ?Carbon
    {
        $sessions = $this->sessions->query(true)
            ->whereNotNull('started_at')
            ->where('watch_seconds', '>', 0)
            ->get(['started_at', 'watch_seconds']);

        if ($sessions->isEmpty()) {
            return null;
        }

        $bestKey = $sessions
            ->groupBy(fn ($session): string => $session->started_at->format('Y-m'))
            ->map(fn ($group): int => (int) $group->sum('watch_seconds'))
            ->sortDesc()
            ->keys()
            ->first();

        if (! is_string($bestKey) || $bestKey === '') {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d', $bestKey.'-01')->startOfMonth();
    }

    public function render()
    {
        return view('livewire.dashboard.revenue.index', [
            'periods' => $this->periods->getWith(
                modify: fn ($query) => $query->withCount('allocations')->latest('period_start'),
                limit: 12,
            ),
            'allocationsCount' => $this->allocations->count(withoutGlobalScopes: true),
            'ledgerCount' => $this->ledgerEntries->count(withoutGlobalScopes: true),
            'showDemoProcess' => ! app()->isProduction(),
        ]);
    }
}

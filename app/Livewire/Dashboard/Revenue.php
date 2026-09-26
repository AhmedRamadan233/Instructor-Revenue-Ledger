<?php

namespace App\Livewire\Dashboard;

use App\Actions\Revenue\ProcessRevenuePeriod;
use App\Livewire\Requests\Dashboard\ProcessRevenueRequest;
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

    public int $year;

    public int $month;

    public function boot(
        RevenuePeriodRepositoryInterface $periods,
        RevenueAllocationRepositoryInterface $allocations,
        TeacherLedgerEntryRepositoryInterface $ledgerEntries,
    ): void {
        $this->periods = $periods;
        $this->allocations = $allocations;
        $this->ledgerEntries = $ledgerEntries;
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

    public function processForDemo(ProcessRevenuePeriod $processor): void
    {
        $this->runProcess($processor, force: true);
    }

    protected function runProcess(ProcessRevenuePeriod $processor, bool $force): void
    {
        $this->validate(ProcessRevenueRequest::rules());

        $periodStart = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        $result = $processor->handle($periodStart, $periodEnd, force: $force);

        $label = $periodStart->format('F Y');
        $prefix = $force ? '[Demo re-run] ' : '';

        if ($result['allocations'] === 0 && $result['ledger_entries'] === 0) {
            if ($result['subscriptions_considered'] === 0) {
                session()->flash(
                    'error',
                    sprintf(
                        '%s%s closed with 0 allocations: no subscription overlapped this month. Example: if watches happened in September, process September — not August. Subscription revenue on Reports can still show the full plan price even when this month had nothing to split.',
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
                        '%s%s closed: %d subscription(s) had pool money but 0 watch seconds in this month, so the pool was carried forward. Teachers get $0 until a later month with watching is processed.',
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
                    '%s%s closed with 0 allocations and 0 carry: overlapping subscriptions had a $0 teacher pool for this month.',
                    $prefix,
                    $label,
                ),
            );

            return;
        }

        session()->flash(
            'success',
            sprintf(
                '%s%s processed: %d allocations, %d ledger entries, %d subscriptions carried forward (no watch time). Reports / teacher balances / payouts update from these ledger earnings.',
                $prefix,
                $label,
                $result['allocations'],
                $result['ledger_entries'],
                $result['carried_forward'],
            ),
        );
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

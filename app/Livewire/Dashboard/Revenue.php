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

        session()->flash(
            'success',
            sprintf(
                '%s%s processed: %d allocations, %d ledger entries, %d subscriptions carried forward (no watch time).',
                $force ? '[Demo re-run] ' : '',
                $periodStart->format('Y-m'),
                $result['allocations'],
                $result['ledger_entries'],
                $result['carried_forward']
            )
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

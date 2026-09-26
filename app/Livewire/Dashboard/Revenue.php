<?php

namespace App\Livewire\Dashboard;

use App\Actions\Revenue\ProcessRevenuePeriod;
use App\Livewire\Requests\Dashboard\ProcessRevenueRequest;
use App\Models\RevenueAllocation;
use App\Models\RevenuePeriod;
use App\Models\TeacherLedgerEntry;
use Carbon\Carbon;
use Livewire\Attributes\Title;

#[Title('Revenue')]
class Revenue extends __AbstractManagerComponent
{
    public int $year;

    public int $month;

    public function mount(): void
    {
        $previous = now()->subMonthNoOverflow();
        $this->year = $previous->year;
        $this->month = $previous->month;
    }

    public function process(ProcessRevenuePeriod $processor): void
    {
        $this->validate(ProcessRevenueRequest::rules());

        $periodStart = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        $result = $processor->handle($periodStart, $periodEnd);

        session()->flash(
            'success',
            sprintf(
                'Period %s processed: %d allocations, %d ledger entries, %d subscriptions carried forward (no watch time).',
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
            'periods' => RevenuePeriod::query()
                ->withCount('allocations')
                ->latest('period_start')
                ->limit(12)
                ->get(),
            'allocationsCount' => RevenueAllocation::query()->withoutGlobalScopes()->count(),
            'ledgerCount' => TeacherLedgerEntry::query()->withoutGlobalScopes()->count(),
        ]);
    }
}

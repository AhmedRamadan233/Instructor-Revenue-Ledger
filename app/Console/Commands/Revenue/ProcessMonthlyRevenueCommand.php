<?php

namespace App\Console\Commands\Revenue;

use App\Actions\Revenue\ProcessRevenuePeriod;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

class ProcessMonthlyRevenueCommand extends Command
{
    protected $signature = 'revenue:process
                            {--year= : Year of the period (default: previous month year)}
                            {--month= : Month of the period 1-12 (default: previous month)}';

    protected $description = 'Process monthly teacher revenue allocations from course consumption';

    public function handle(ProcessRevenuePeriod $processor): int
    {
        $reference = now()->subMonthNoOverflow();
        $year = (int) ($this->option('year') ?: $reference->year);
        $month = (int) ($this->option('month') ?: $reference->month);

        $periodStart = Carbon::create($year, $month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();

        $this->info(sprintf(
            'Processing revenue period %s → %s ...',
            $periodStart->toDateString(),
            $periodEnd->toDateString()
        ));

        try {
            $result = $processor->handle($periodStart, $periodEnd);
        } catch (ValidationException $exception) {
            $this->error(collect($exception->errors())->flatten()->first() ?: 'Validation failed.');

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Done. Period #%d processed with %d allocations, %d ledger entries, %d subscriptions carried forward.',
            $result['period']->id,
            $result['allocations'],
            $result['ledger_entries'],
            $result['carried_forward']
        ));

        return self::SUCCESS;
    }
}

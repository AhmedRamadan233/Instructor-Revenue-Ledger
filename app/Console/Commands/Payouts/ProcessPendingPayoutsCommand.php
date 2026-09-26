<?php

namespace App\Console\Commands\Payouts;

use App\Enums\PayoutStatus;
use App\Jobs\Payouts\ProcessPayoutJob;
use App\Repo\InterFace\PayoutRepositoryInterface;
use Illuminate\Console\Command;

class ProcessPendingPayoutsCommand extends Command
{
    protected $signature = 'payouts:process
                            {--sync : Run inline instead of queueing}
                            {--reconcile : Re-check Processing payouts that already have a provider reference}';

    protected $description = 'Send pending instructor payouts through the payment provider (queued by default)';

    public function handle(PayoutRepositoryInterface $payouts): int
    {
        if ($this->option('reconcile')) {
            return $this->reconcileProcessing($payouts);
        }

        $pending = $payouts->getWhere(
            ['status' => PayoutStatus::Pending],
            withoutGlobalScopes: true,
        );

        if ($pending->isEmpty()) {
            $this->info('No pending payouts.');

            return self::SUCCESS;
        }

        foreach ($pending as $payout) {
            if ($this->option('sync')) {
                ProcessPayoutJob::dispatchSync($payout->id);
                $this->line("Processed payout #{$payout->id} synchronously.");
            } else {
                ProcessPayoutJob::dispatch($payout->id);
                $this->line("Queued payout #{$payout->id}.");
            }
        }

        $this->info("Dispatched {$pending->count()} payout(s).");

        return self::SUCCESS;
    }

    protected function reconcileProcessing(PayoutRepositoryInterface $payouts): int
    {
        $processing = $payouts->query(true)
            ->where('status', PayoutStatus::Processing)
            ->whereNotNull('provider_reference')
            ->get();

        if ($processing->isEmpty()) {
            $this->info('No processing payouts to reconcile.');

            return self::SUCCESS;
        }

        foreach ($processing as $payout) {
            if ($this->option('sync')) {
                ProcessPayoutJob::dispatchSync($payout->id);
            } else {
                ProcessPayoutJob::dispatch($payout->id);
            }

            $this->line("Reconciliation queued for payout #{$payout->id}.");
        }

        $this->info("Dispatched {$processing->count()} reconciliation job(s).");

        return self::SUCCESS;
    }
}

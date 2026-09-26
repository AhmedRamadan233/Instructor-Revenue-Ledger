<?php

namespace App\Jobs\Payouts;

use App\Actions\Payouts\ProcessPayoutWithProvider;
use App\Enums\PayoutStatus;
use App\Models\Payout;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class ProcessPayoutJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    /** @var list<int> */
    public array $backoff = [5, 15, 30, 60];

    public int $uniqueFor = 3600;

    public function __construct(public int $payoutId) {}

    public function uniqueId(): string
    {
        return 'process-payout-'.$this->payoutId;
    }

    /**
     * @return list<object>
     */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping('payout-'.$this->payoutId))->releaseAfter(30),
        ];
    }

    public function handle(ProcessPayoutWithProvider $processor): void
    {
        $processor->handle($this->payoutId);
    }

    public function failed(?\Throwable $exception): void
    {
        $payout = Payout::query()->withoutGlobalScopes()->find($this->payoutId);

        if ($payout === null || $payout->status === PayoutStatus::Paid) {
            return;
        }

        if ($payout->status === PayoutStatus::Processing && filled($payout->provider_reference)) {
            // Leave in Processing for a later reconcile pass; money may already have moved.
            $payout->forceFill([
                'failure_reason' => $exception?->getMessage(),
            ])->save();

            return;
        }

        if ($payout->status !== PayoutStatus::Failed) {
            $payout->forceFill([
                'status' => PayoutStatus::Failed,
                'failure_reason' => $exception?->getMessage(),
                'processed_at' => now(),
            ])->save();
        }
    }
}

<?php

namespace App\Actions\Payouts;

use App\Enums\LedgerEntryType;
use App\Enums\PayoutStatus;
use App\Models\Payout;
use App\Payments\Contracts\PaymentProvider;
use App\Payments\Enums\ProviderOutcome;
use App\Repo\InterFace\PayoutRepositoryInterface;
use App\Repo\InterFace\TeacherLedgerEntryRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class ProcessPayoutWithProvider
{
    public function __construct(
        private PayoutRepositoryInterface $payouts,
        private TeacherLedgerEntryRepositoryInterface $ledgerEntries,
        private PaymentProvider $provider,
    ) {}

    public function handle(int $payoutId): Payout
    {
        $payout = $this->claimForProcessing($payoutId);

        if ($payout->status === PayoutStatus::Paid) {
            return $payout;
        }

        if ($payout->status === PayoutStatus::Processing && filled($payout->provider_reference)) {
            return $this->reconcile($payout->id);
        }

        try {
            $result = $this->provider->pay(
                idempotencyKey: (string) $payout->idempotency_key,
                teacher: $payout->teacher()->withoutGlobalScopes()->firstOrFail(),
                amount: (float) $payout->amount,
                currency: $payout->currency,
            );

            return $this->finalizeFromProviderResult(
                $payout->id,
                $result->providerReference,
                $result->outcome,
                $result->message,
            );
        } catch (Throwable $exception) {
            return $this->reconcileAfterUncertainty($payout->id, $exception);
        }
    }

    public function reconcile(int $payoutId): Payout
    {
        return DB::transaction(function () use ($payoutId): Payout {
            $payout = $this->payouts->lockForUpdateById($payoutId, withoutGlobalScopes: true);

            if ($payout->status === PayoutStatus::Paid) {
                return $payout;
            }

            if ($payout->provider_reference === null) {
                throw ValidationException::withMessages([
                    'payout' => 'Cannot reconcile a payout without a provider reference.',
                ]);
            }

            $result = $this->provider->status($payout->provider_reference);

            return $this->finalizeFromProviderResult(
                $payout->id,
                $result->providerReference,
                $result->outcome,
                $result->message,
            );
        });
    }

    protected function claimForProcessing(int $payoutId): Payout
    {
        return DB::transaction(function () use ($payoutId): Payout {
            $payout = $this->payouts->lockForUpdateById($payoutId, withoutGlobalScopes: true);

            if (in_array($payout->status, [PayoutStatus::Paid, PayoutStatus::Processing], true)) {
                if ($payout->idempotency_key === null) {
                    $this->payouts->update($payout->id, [
                        'idempotency_key' => 'payout-'.$payout->id,
                    ], withoutGlobalScopes: true);

                    return $payout->refresh();
                }

                return $payout;
            }

            if ($payout->status === PayoutStatus::Rejected) {
                throw ValidationException::withMessages([
                    'payout' => 'Rejected payouts cannot be sent to the provider.',
                ]);
            }

            if ($payout->status === PayoutStatus::Failed) {
                throw ValidationException::withMessages([
                    'payout' => 'Failed payouts must be reviewed before retrying.',
                ]);
            }

            $this->payouts->update($payout->id, [
                'status' => PayoutStatus::Processing,
                'idempotency_key' => $payout->idempotency_key ?? 'payout-'.$payout->id,
                'failure_reason' => null,
            ], withoutGlobalScopes: true);

            return $payout->refresh();
        });
    }

    protected function reconcileAfterUncertainty(int $payoutId, Throwable $exception): Payout
    {
        $payout = $this->payouts->find($payoutId, withoutGlobalScopes: true);

        if ($payout === null) {
            throw $exception;
        }

        $reference = $payout->provider_reference;

        if ($reference === null && preg_match('/Reference:\s*(\S+)/', $exception->getMessage(), $matches) === 1) {
            $reference = $matches[1];
            $this->payouts->update($payout->id, [
                'provider_reference' => $reference,
                'provider_status' => ProviderOutcome::TimeoutAfterSuccess->value,
                'failure_reason' => $exception->getMessage(),
                'last_provider_checked_at' => now(),
            ], withoutGlobalScopes: true);
        }

        if ($reference === null) {
            $this->payouts->update($payout->id, [
                'status' => PayoutStatus::Failed,
                'failure_reason' => $exception->getMessage(),
                'processed_at' => now(),
            ], withoutGlobalScopes: true);

            throw $exception;
        }

        // Money may already have moved — keep Processing and let a retry/reconcile confirm via status().
        $this->payouts->update($payout->id, [
            'status' => PayoutStatus::Processing,
            'provider_reference' => $reference,
            'provider_status' => ProviderOutcome::TimeoutAfterSuccess->value,
            'failure_reason' => $exception->getMessage(),
            'last_provider_checked_at' => now(),
        ], withoutGlobalScopes: true);

        throw $exception;
    }

    protected function finalizeFromProviderResult(
        int $payoutId,
        string $providerReference,
        ProviderOutcome $outcome,
        ?string $message,
    ): Payout {
        return DB::transaction(function () use ($payoutId, $providerReference, $outcome, $message): Payout {
            $payout = $this->payouts->lockForUpdateById($payoutId, withoutGlobalScopes: true);

            if ($payout->status === PayoutStatus::Paid) {
                return $payout;
            }

            $this->payouts->update($payout->id, [
                'provider_reference' => $providerReference,
                'provider_status' => $outcome->value,
                'last_provider_checked_at' => now(),
                'failure_reason' => $outcome === ProviderOutcome::Failed ? $message : null,
            ], withoutGlobalScopes: true);

            if ($outcome === ProviderOutcome::Failed) {
                $this->payouts->update($payout->id, [
                    'status' => PayoutStatus::Failed,
                    'processed_at' => now(),
                ], withoutGlobalScopes: true);

                return $payout->refresh();
            }

            if ($outcome === ProviderOutcome::TimeoutAfterSuccess) {
                $this->payouts->update($payout->id, [
                    'status' => PayoutStatus::Processing,
                    'failure_reason' => $message,
                ], withoutGlobalScopes: true);

                throw new RuntimeException($message ?? 'Provider timed out; payout left in processing for reconciliation.');
            }

            $alreadyPosted = $this->ledgerEntries->query(true)
                ->where('teacher_id', $payout->teacher_id)
                ->where('reference_type', Payout::class)
                ->where('reference_id', $payout->id)
                ->where('type', LedgerEntryType::Payout)
                ->exists();

            if (! $alreadyPosted) {
                $this->ledgerEntries->create([
                    'teacher_id' => $payout->teacher_id,
                    'type' => LedgerEntryType::Payout,
                    'amount' => $payout->amount,
                    'currency' => $payout->currency,
                    'reference_type' => Payout::class,
                    'reference_id' => $payout->id,
                    'revenue_period_id' => null,
                ]);
            }

            $this->payouts->update($payout->id, [
                'status' => PayoutStatus::Paid,
                'processed_at' => now(),
                'failure_reason' => null,
            ], withoutGlobalScopes: true);

            return $payout->refresh();
        });
    }
}

<?php

namespace App\Payments;

use App\Models\Teacher;
use App\Payments\Contracts\PaymentProvider;
use App\Payments\Data\ProviderTransferResult;
use App\Payments\Enums\ProviderOutcome;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Deterministic-enough mock for challenge demos and tests.
 *
 * Outcomes:
 * - succeeded
 * - failed (permanent)
 * - timeout_after_success (money already moved; pay() throws/returns uncertain; status() confirms)
 *
 * Force an outcome via config('payouts.mock.forced_outcome') or
 * Cache::put('payouts.mock.next_outcome', 'failed').
 */
class MockPaymentProvider implements PaymentProvider
{
    private const CACHE_TRANSFERS = 'payouts.mock.transfers';

    private const CACHE_BY_IDEMPOTENCY = 'payouts.mock.by_idempotency';

    private const CACHE_NEXT_OUTCOME = 'payouts.mock.next_outcome';

    public function pay(
        string $idempotencyKey,
        Teacher $teacher,
        float $amount,
        string $currency,
    ): ProviderTransferResult {
        $existingKey = $this->idempotencyMap()[$idempotencyKey] ?? null;

        if (is_string($existingKey) && $existingKey !== '') {
            return $this->status($existingKey);
        }

        $outcome = $this->resolveOutcome();
        $reference = 'mock_'.Str::lower(Str::ulid());

        $result = new ProviderTransferResult(
            outcome: $outcome,
            providerReference: $reference,
            idempotencyKey: $idempotencyKey,
            fundsMoved: $outcome !== ProviderOutcome::Failed,
            message: match ($outcome) {
                ProviderOutcome::Succeeded => 'Transfer completed.',
                ProviderOutcome::Failed => 'Provider permanently rejected the transfer.',
                ProviderOutcome::TimeoutAfterSuccess => 'Provider timed out after accepting the transfer.',
            },
        );

        $this->store($result);

        if ($outcome === ProviderOutcome::TimeoutAfterSuccess) {
            throw new RuntimeException(
                'Payment provider timed out after accepting the transfer. Reconcile via status(). Reference: '.$reference
            );
        }

        return $result;
    }

    public function status(string $providerReference): ProviderTransferResult
    {
        $stored = $this->transfers()[$providerReference] ?? null;

        if (! is_array($stored)) {
            throw new RuntimeException('Unknown provider reference: '.$providerReference);
        }

        $outcome = ProviderOutcome::from($stored['outcome']);

        // After timeout, a later status check reveals the money already moved.
        if ($outcome === ProviderOutcome::TimeoutAfterSuccess) {
            $outcome = ProviderOutcome::Succeeded;
        }

        return new ProviderTransferResult(
            outcome: $outcome,
            providerReference: $providerReference,
            idempotencyKey: $stored['idempotency_key'],
            fundsMoved: (bool) $stored['funds_moved'],
            message: $stored['message'] ?? null,
        );
    }

    public function forceNextOutcome(ProviderOutcome $outcome): void
    {
        Cache::put(self::CACHE_NEXT_OUTCOME, $outcome->value, now()->addHour());
    }

    public function reset(): void
    {
        Cache::forget(self::CACHE_TRANSFERS);
        Cache::forget(self::CACHE_BY_IDEMPOTENCY);
        Cache::forget(self::CACHE_NEXT_OUTCOME);
    }

    protected function resolveOutcome(): ProviderOutcome
    {
        $forced = Cache::pull(self::CACHE_NEXT_OUTCOME)
            ?? config('payouts.mock.forced_outcome');

        if (is_string($forced) && $forced !== '') {
            return ProviderOutcome::from($forced);
        }

        $roll = random_int(1, 100);

        return match (true) {
            $roll <= (int) config('payouts.mock.chance_failed', 20) => ProviderOutcome::Failed,
            $roll <= (int) config('payouts.mock.chance_failed', 20) + (int) config('payouts.mock.chance_timeout', 20) => ProviderOutcome::TimeoutAfterSuccess,
            default => ProviderOutcome::Succeeded,
        };
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function store(ProviderTransferResult $result): void
    {
        $transfers = $this->transfers();
        $transfers[$result->providerReference] = [
            'outcome' => $result->outcome->value,
            'idempotency_key' => $result->idempotencyKey,
            'funds_moved' => $result->fundsMoved,
            'message' => $result->message,
        ];
        Cache::forever(self::CACHE_TRANSFERS, $transfers);

        $map = $this->idempotencyMap();
        $map[$result->idempotencyKey] = $result->providerReference;
        Cache::forever(self::CACHE_BY_IDEMPOTENCY, $map);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function transfers(): array
    {
        /** @var array<string, array<string, mixed>> $transfers */
        $transfers = Cache::get(self::CACHE_TRANSFERS, []);

        return $transfers;
    }

    /**
     * @return array<string, string>
     */
    protected function idempotencyMap(): array
    {
        /** @var array<string, string> $map */
        $map = Cache::get(self::CACHE_BY_IDEMPOTENCY, []);

        return $map;
    }
}

<?php

namespace App\Payments\Data;

use App\Payments\Enums\ProviderOutcome;

final readonly class ProviderTransferResult
{
    public function __construct(
        public ProviderOutcome $outcome,
        public string $providerReference,
        public string $idempotencyKey,
        public bool $fundsMoved,
        public ?string $message = null,
    ) {}

    public function isTerminalSuccess(): bool
    {
        return $this->outcome === ProviderOutcome::Succeeded
            || ($this->outcome === ProviderOutcome::TimeoutAfterSuccess && $this->fundsMoved);
    }

    public function isPermanentFailure(): bool
    {
        return $this->outcome === ProviderOutcome::Failed;
    }

    public function needsStatusReconciliation(): bool
    {
        return $this->outcome === ProviderOutcome::TimeoutAfterSuccess;
    }
}

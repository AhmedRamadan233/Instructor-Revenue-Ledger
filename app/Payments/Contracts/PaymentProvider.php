<?php

namespace App\Payments\Contracts;

use App\Models\Teacher;
use App\Payments\Data\ProviderTransferResult;

interface PaymentProvider
{
    /**
     * Attempt to move money to the instructor.
     * Must be idempotent for the same $idempotencyKey.
     */
    public function pay(
        string $idempotencyKey,
        Teacher $teacher,
        float $amount,
        string $currency,
    ): ProviderTransferResult;

    /**
     * Discover the real outcome after an uncertain response (e.g. timeout).
     */
    public function status(string $providerReference): ProviderTransferResult;
}

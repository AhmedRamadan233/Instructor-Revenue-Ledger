<?php

namespace App\Support;

use App\Enums\LedgerEntryType;
use App\Enums\PayoutStatus;
use App\Models\Teacher;
use App\Repo\InterFace\PayoutRepositoryInterface;
use App\Repo\InterFace\TeacherLedgerEntryRepositoryInterface;

final class TeacherBalance
{
    public static function available(Teacher $teacher, string $currency = 'EGP'): float
    {
        /** @var TeacherLedgerEntryRepositoryInterface $ledger */
        $ledger = app(TeacherLedgerEntryRepositoryInterface::class);
        /** @var PayoutRepositoryInterface $payouts */
        $payouts = app(PayoutRepositoryInterface::class);

        $base = [
            'teacher_id' => $teacher->id,
            'currency' => $currency,
        ];

        $earned = $ledger->sum('amount', [...$base, 'type' => LedgerEntryType::Earning], true);
        $refunds = $ledger->sum('amount', [...$base, 'type' => LedgerEntryType::Refund], true);
        $adjustments = $ledger->sum('amount', [...$base, 'type' => LedgerEntryType::Adjustment], true);
        $paidOut = $ledger->sum('amount', [...$base, 'type' => LedgerEntryType::Payout], true);
        $reserved = $payouts->sum('amount', [
            ...$base,
            'status' => [PayoutStatus::Pending, PayoutStatus::Processing],
        ], true);

        return round($earned + $adjustments - $refunds - $paidOut - $reserved, 2);
    }
}

<?php

namespace App\Support;

use App\Enums\LedgerEntryType;
use App\Enums\PayoutStatus;
use App\Models\Payout;
use App\Models\Teacher;
use App\Models\TeacherLedgerEntry;

final class TeacherBalance
{
    public static function available(Teacher $teacher, string $currency = 'EGP'): float
    {
        $earned = (float) TeacherLedgerEntry::query()
            ->withoutGlobalScopes()
            ->where('teacher_id', $teacher->id)
            ->where('currency', $currency)
            ->where('type', LedgerEntryType::Earning)
            ->sum('amount');

        $refunds = (float) TeacherLedgerEntry::query()
            ->withoutGlobalScopes()
            ->where('teacher_id', $teacher->id)
            ->where('currency', $currency)
            ->where('type', LedgerEntryType::Refund)
            ->sum('amount');

        $adjustments = (float) TeacherLedgerEntry::query()
            ->withoutGlobalScopes()
            ->where('teacher_id', $teacher->id)
            ->where('currency', $currency)
            ->where('type', LedgerEntryType::Adjustment)
            ->sum('amount');

        $paidOut = (float) TeacherLedgerEntry::query()
            ->withoutGlobalScopes()
            ->where('teacher_id', $teacher->id)
            ->where('currency', $currency)
            ->where('type', LedgerEntryType::Payout)
            ->sum('amount');

        $reserved = (float) Payout::query()
            ->withoutGlobalScopes()
            ->where('teacher_id', $teacher->id)
            ->where('currency', $currency)
            ->where('status', PayoutStatus::Pending)
            ->sum('amount');

        return round($earned + $adjustments - $refunds - $paidOut - $reserved, 2);
    }
}

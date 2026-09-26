<?php

namespace App\Actions\Payouts;

use App\Enums\LedgerEntryType;
use App\Enums\PayoutStatus;
use App\Models\Manager;
use App\Models\Payout;
use App\Models\TeacherLedgerEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MarkPayoutPaid
{
    public function handle(Payout $payout, Manager $manager, ?string $note = null): Payout
    {
        return DB::transaction(function () use ($payout, $manager, $note): Payout {
            $payout = Payout::query()
                ->withoutGlobalScopes()
                ->whereKey($payout->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($payout->status !== PayoutStatus::Pending) {
                throw ValidationException::withMessages([
                    'payout' => 'Only pending payouts can be marked as paid.',
                ]);
            }

            $payout->update([
                'status' => PayoutStatus::Paid,
                'note' => $note ?? $payout->note,
                'processed_at' => now(),
                'processed_by_manager_id' => $manager->id,
            ]);

            TeacherLedgerEntry::query()->create([
                'teacher_id' => $payout->teacher_id,
                'type' => LedgerEntryType::Payout,
                'amount' => $payout->amount,
                'currency' => $payout->currency,
                'reference_type' => Payout::class,
                'reference_id' => $payout->id,
                'revenue_period_id' => null,
            ]);

            return $payout->refresh();
        });
    }
}

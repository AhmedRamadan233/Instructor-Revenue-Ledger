<?php

namespace App\Actions\Payouts;

use App\Enums\PayoutStatus;
use App\Models\Manager;
use App\Models\Payout;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RejectPayout
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
                    'payout' => 'Only pending payouts can be rejected.',
                ]);
            }

            $payout->update([
                'status' => PayoutStatus::Rejected,
                'note' => $note ?? $payout->note,
                'processed_at' => now(),
                'processed_by_manager_id' => $manager->id,
            ]);

            return $payout->refresh();
        });
    }
}

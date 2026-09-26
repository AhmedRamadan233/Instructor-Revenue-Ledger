<?php

namespace App\Actions\Payouts;

use App\Enums\PayoutStatus;
use App\Models\Manager;
use App\Models\Payout;
use App\Repo\InterFace\PayoutRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RejectPayout
{
    public function __construct(
        private PayoutRepositoryInterface $payouts,
    ) {}

    public function handle(Payout $payout, Manager $manager, ?string $note = null): Payout
    {
        return DB::transaction(function () use ($payout, $manager, $note): Payout {
            $payout = $this->payouts->lockForUpdateById($payout->id, withoutGlobalScopes: true);

            if ($payout->status !== PayoutStatus::Pending) {
                throw ValidationException::withMessages([
                    'payout' => 'Only pending payouts can be rejected.',
                ]);
            }

            $this->payouts->update($payout->id, [
                'status' => PayoutStatus::Rejected,
                'note' => $note ?? $payout->note,
                'processed_at' => now(),
                'processed_by_manager_id' => $manager->id,
            ], withoutGlobalScopes: true);

            return $payout->refresh();
        });
    }
}

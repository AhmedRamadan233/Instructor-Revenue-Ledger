<?php

namespace App\Actions\Payouts;

use App\Enums\PayoutStatus;
use App\Models\Payout;
use App\Models\Teacher;
use App\Support\TeacherBalance;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RequestPayout
{
    public function handle(Teacher $teacher, float $amount, string $currency = 'EGP', ?string $note = null): Payout
    {
        $amount = round($amount, 2);

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Payout amount must be greater than zero.',
            ]);
        }

        return DB::transaction(function () use ($teacher, $amount, $currency, $note): Payout {
            $teacher = Teacher::query()
                ->withoutGlobalScopes()
                ->whereKey($teacher->id)
                ->lockForUpdate()
                ->firstOrFail();

            $available = TeacherBalance::available($teacher, $currency);

            if ($amount > $available) {
                throw ValidationException::withMessages([
                    'amount' => sprintf(
                        'Requested amount exceeds available balance (%.2f %s).',
                        $available,
                        $currency
                    ),
                ]);
            }

            return Payout::query()->create([
                'teacher_id' => $teacher->id,
                'amount' => $amount,
                'currency' => $currency,
                'status' => PayoutStatus::Pending,
                'note' => $note,
                'requested_at' => now(),
            ]);
        });
    }
}

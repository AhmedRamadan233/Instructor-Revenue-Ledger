<?php

namespace Database\Factories;

use App\Enums\LedgerEntryType;
use App\Models\Teacher;
use App\Models\TeacherLedgerEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeacherLedgerEntry>
 */
class TeacherLedgerEntryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'teacher_id' => Teacher::factory(),
            'type' => LedgerEntryType::Earning,
            'amount' => 480.00,
            'currency' => 'EGP',
            'reference_type' => null,
            'reference_id' => null,
            'revenue_period_id' => null,
        ];
    }
}

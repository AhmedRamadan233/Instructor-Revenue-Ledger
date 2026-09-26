<?php

namespace App\Models;

use App\Enums\LedgerEntryType;
use Database\Factories\TeacherLedgerEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'teacher_id',
    'type',
    'amount',
    'currency',
    'reference_type',
    'reference_id',
    'revenue_period_id',
])]
class TeacherLedgerEntry extends Model
{
    /** @use HasFactory<TeacherLedgerEntryFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => LedgerEntryType::class,
            'amount' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function revenuePeriod(): BelongsTo
    {
        return $this->belongsTo(RevenuePeriod::class);
    }
}

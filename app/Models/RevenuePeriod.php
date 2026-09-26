<?php

namespace App\Models;

use App\Enums\RevenuePeriodStatus;
use App\Models\Scopes\Global\RevenuePeriodAccessScope;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy([RevenuePeriodAccessScope::class])]
#[Fillable([
    'period_start',
    'period_end',
    'status',
    'processed_at',
])]
class RevenuePeriod extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'status' => RevenuePeriodStatus::class,
            'processed_at' => 'datetime',
        ];
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(RevenueAllocation::class);
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(TeacherLedgerEntry::class);
    }
}

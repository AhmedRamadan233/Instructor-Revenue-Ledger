<?php

namespace App\Models;

use App\Models\Scopes\BelongsToAuthenticatedTeacherScope;
use Database\Factories\RevenueAllocationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy([BelongsToAuthenticatedTeacherScope::class])]
#[Fillable([
    'revenue_period_id',
    'subscription_id',
    'teacher_id',
    'consumption_seconds',
    'total_consumption_seconds',
    'allocated_amount',
    'currency',
])]
class RevenueAllocation extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'consumption_seconds' => 'integer',
            'total_consumption_seconds' => 'integer',
            'allocated_amount' => 'decimal:2',
        ];
    }

    public function revenuePeriod(): BelongsTo
    {
        return $this->belongsTo(RevenuePeriod::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}

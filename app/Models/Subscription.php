<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use App\Models\Scopes\Global\BelongsToAuthenticatedStudentScope;
use App\Models\Scopes\Local\SubscriptionScopes;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy([BelongsToAuthenticatedStudentScope::class])]
#[Fillable([
    'student_id',
    'plan_option_id',
    'status',
    'starts_at',
    'ends_at',
    'amount',
    'currency',
    'platform_percentage',
    'platform_amount',
    'teacher_pool_amount',
    'paid_at',
])]
class Subscription extends Model
{
    use HasFactory, SubscriptionScopes;

    protected function casts(): array
    {
        return [
            'status' => SubscriptionStatus::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'amount' => 'decimal:2',
            'platform_percentage' => 'decimal:2',
            'platform_amount' => 'decimal:2',
            'teacher_pool_amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function planOption(): BelongsTo
    {
        return $this->belongsTo(PlanOption::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    public function consumptionSessions(): HasMany
    {
        return $this->hasMany(CourseConsumptionSession::class);
    }

    public function revenueAllocations(): HasMany
    {
        return $this->hasMany(RevenueAllocation::class);
    }
}

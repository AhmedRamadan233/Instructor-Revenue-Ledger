<?php

namespace App\Models;

use App\Enums\PayoutStatus;
use App\Models\Scopes\Global\BelongsToAuthenticatedTeacherScope;
use App\Models\Scopes\Local\PayoutScopes;
use Database\Factories\PayoutFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy([BelongsToAuthenticatedTeacherScope::class])]
#[Fillable([
    'teacher_id',
    'idempotency_key',
    'amount',
    'currency',
    'status',
    'note',
    'requested_at',
    'processed_at',
    'processed_by_manager_id',
    'provider_reference',
    'provider_status',
    'failure_reason',
    'last_provider_checked_at',
])]
class Payout extends Model
{
    /** @use HasFactory<PayoutFactory> */
    use HasFactory, PayoutScopes;

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => PayoutStatus::class,
            'requested_at' => 'datetime',
            'processed_at' => 'datetime',
            'last_provider_checked_at' => 'datetime',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function processedByManager(): BelongsTo
    {
        return $this->belongsTo(Manager::class, 'processed_by_manager_id');
    }
}

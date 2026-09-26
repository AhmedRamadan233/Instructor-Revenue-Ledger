<?php

namespace App\Models;

use App\Enums\SubscriptionPaymentStatus;
use App\Models\Scopes\SubscriptionPaymentAccessScope;
use Database\Factories\SubscriptionPaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy([SubscriptionPaymentAccessScope::class])]
#[Fillable([
    'subscription_id',
    'amount',
    'currency',
    'status',
    'provider',
    'provider_reference',
    'paid_at',
])]
class SubscriptionPayment extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => SubscriptionPaymentStatus::class,
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}

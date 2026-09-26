<?php

namespace App\Models;

use App\Enums\PlanType;
use Database\Factories\PlanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'type',
    'price',
    'currency',
    'duration_months',
    'is_active',
])]
class Plan extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => PlanType::class,
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'duration_months' => 'integer',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}

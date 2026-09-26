<?php

namespace App\Models\Scopes\Local;

use App\Models\RevenueAllocation;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait RevenueAllocationScopes
{
    use __AppliesLocalScopes;

    /**
     * @param  Builder<RevenueAllocation>  $query
     * @return Builder<RevenueAllocation>
     */
    #[Scope]
    protected function search(Builder $query, ?string $term): Builder
    {
        return $this->applySearch($query, $term, [
            'currency',
            'allocated_amount',
            'consumption_seconds',
        ]);
    }
}

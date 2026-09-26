<?php

namespace App\Models\Scopes\Local;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait PlanScopes
{
    use __AppliesLocalScopes;

    /**
     * @param  Builder<Plan>  $query
     * @return Builder<Plan>
     */
    #[Scope]
    protected function search(Builder $query, ?string $term): Builder
    {
        return $this->applySearch($query, $term, ['name', 'description']);
    }

    /**
     * @param  Builder<Plan>  $query
     * @return Builder<Plan>
     */
    #[Scope]
    protected function active(Builder $query, null|bool|string $active = true): Builder
    {
        if ($active === null || $active === '') {
            return $query;
        }

        $value = filter_var($active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($value === null) {
            return $query;
        }

        return $query->where($query->getModel()->qualifyColumn('is_active'), $value);
    }
}

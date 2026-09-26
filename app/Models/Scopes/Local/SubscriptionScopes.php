<?php

namespace App\Models\Scopes\Local;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait SubscriptionScopes
{
    use __AppliesLocalScopes;

    /**
     * @param  Builder<Subscription>  $query
     * @return Builder<Subscription>
     */
    #[Scope]
    protected function search(Builder $query, ?string $term): Builder
    {
        return $this->applySearch(
            $query,
            $term,
            ['currency', 'amount'],
            function (Builder $query, string $term): void {
                $query->orWhereHas('planOption.plan', function (Builder $planQuery) use ($term): void {
                    $planQuery->where('name', 'like', "%{$term}%");
                });
            },
        );
    }

    /**
     * @param  Builder<Subscription>  $query
     * @return Builder<Subscription>
     */
    #[Scope]
    protected function status(Builder $query, null|int|string|SubscriptionStatus $status): Builder
    {
        return $this->applyEnumFilter($query, 'status', $status, SubscriptionStatus::class);
    }
}

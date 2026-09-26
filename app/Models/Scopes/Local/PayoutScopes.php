<?php

namespace App\Models\Scopes\Local;

use App\Enums\PayoutStatus;
use App\Models\Payout;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait PayoutScopes
{
    use __AppliesLocalScopes;

    /**
     * @param  Builder<Payout>  $query
     * @return Builder<Payout>
     */
    #[Scope]
    protected function search(Builder $query, ?string $term): Builder
    {
        return $this->applySearch($query, $term, ['currency', 'amount', 'note']);
    }

    /**
     * @param  Builder<Payout>  $query
     * @return Builder<Payout>
     */
    #[Scope]
    protected function status(Builder $query, null|int|string|PayoutStatus $status): Builder
    {
        return $this->applyEnumFilter($query, 'status', $status, PayoutStatus::class);
    }
}

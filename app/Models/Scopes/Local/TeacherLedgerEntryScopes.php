<?php

namespace App\Models\Scopes\Local;

use App\Enums\LedgerEntryType;
use App\Models\TeacherLedgerEntry;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait TeacherLedgerEntryScopes
{
    use __AppliesLocalScopes;

    /**
     * @param  Builder<TeacherLedgerEntry>  $query
     * @return Builder<TeacherLedgerEntry>
     */
    #[Scope]
    protected function search(Builder $query, ?string $term): Builder
    {
        return $this->applySearch($query, $term, ['currency', 'amount']);
    }

    /**
     * @param  Builder<TeacherLedgerEntry>  $query
     * @return Builder<TeacherLedgerEntry>
     */
    #[Scope]
    protected function type(Builder $query, null|int|string|LedgerEntryType $type): Builder
    {
        return $this->applyEnumFilter($query, 'type', $type, LedgerEntryType::class);
    }
}

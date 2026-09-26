<?php

namespace App\Models\Scopes\Local;

use App\Enums\CourseStatus;
use App\Models\Course;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait CourseScopes
{
    use __AppliesLocalScopes;

    /**
     * @param  Builder<Course>  $query
     * @return Builder<Course>
     */
    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where($query->getModel()->qualifyColumn('status'), CourseStatus::Published);
    }

    /**
     * @param  Builder<Course>  $query
     * @return Builder<Course>
     */
    #[Scope]
    protected function search(Builder $query, ?string $term): Builder
    {
        return $this->applySearch($query, $term, ['title', 'description']);
    }

    /**
     * @param  Builder<Course>  $query
     * @return Builder<Course>
     */
    #[Scope]
    protected function status(Builder $query, null|int|string|CourseStatus $status): Builder
    {
        return $this->applyEnumFilter($query, 'status', $status, CourseStatus::class);
    }
}

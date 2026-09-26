<?php

namespace App\Models\Scopes\Local;

use App\Models\Student;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait StudentScopes
{
    /**
     * @param  Builder<Student>  $query
     * @return Builder<Student>
     */
    #[Scope]
    protected function search(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->whereHas('user', function (Builder $userQuery) use ($term): void {
            $userQuery->where(function (Builder $userQuery) use ($term): void {
                $userQuery->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            });
        });
    }

    /**
     * @param  Builder<Student>  $query
     * @return Builder<Student>
     */
    #[Scope]
    protected function forCourse(Builder $query, int $courseId): Builder
    {
        return $query->whereHas('consumptionSessions', function (Builder $sessionQuery) use ($courseId): void {
            $sessionQuery->where('course_id', $courseId);
        });
    }
}

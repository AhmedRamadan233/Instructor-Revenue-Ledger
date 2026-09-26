<?php

namespace App\Models\Scopes\Local;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait TeacherScopes
{
    /**
     * @param  Builder<Teacher>  $query
     * @return Builder<Teacher>
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
}

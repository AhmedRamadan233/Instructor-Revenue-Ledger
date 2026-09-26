<?php

namespace App\Models;

use App\Enums\CourseStatus;
use App\Models\Scopes\CourseAccessScope;
use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy([CourseAccessScope::class])]
#[Fillable([
    'teacher_id',
    'title',
    'description',
    'status',
])]
class Course extends Model
{
    /** @use HasFactory<CourseFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => CourseStatus::class,
        ];
    }

    /**
     * @param  Builder<Course>  $query
     * @return Builder<Course>
     */
    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), CourseStatus::Published);
    }

    #[Scope]
    protected function search(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($term): void {
            $query->where($this->qualifyColumn('title'), 'like', "%{$term}%")
                ->orWhere($this->qualifyColumn('description'), 'like', "%{$term}%");
        });
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function consumptionSessions(): HasMany
    {
        return $this->hasMany(CourseConsumptionSession::class);
    }
}

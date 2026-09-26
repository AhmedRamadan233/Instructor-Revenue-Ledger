<?php

namespace App\Models;

use App\Enums\CourseStatus;
use App\Models\Scopes\Global\CourseAccessScope;
use App\Models\Scopes\Local\CourseScopes;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
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
    use CourseScopes, HasFactory;

    protected function casts(): array
    {
        return [
            'status' => CourseStatus::class,
        ];
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

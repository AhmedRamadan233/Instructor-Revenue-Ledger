<?php

namespace App\Models;

use App\Models\Scopes\CourseConsumptionSessionAccessScope;
use Database\Factories\CourseConsumptionSessionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy([CourseConsumptionSessionAccessScope::class])]
#[Fillable([
    'student_id',
    'course_id',
    'subscription_id',
    'started_at',
    'last_activity_at',
    'ended_at',
    'watch_seconds',
])]
class CourseConsumptionSession extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'ended_at' => 'datetime',
            'watch_seconds' => 'integer',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}

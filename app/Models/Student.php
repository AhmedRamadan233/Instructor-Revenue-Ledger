<?php

namespace App\Models;

use App\Models\Scopes\Global\StudentProfileScope;
use App\Models\Scopes\Local\StudentScopes;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy([StudentProfileScope::class])]
#[Fillable(['user_id'])]
class Student extends Model
{
    use HasFactory;
    use StudentScopes;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function consumptionSessions(): HasMany
    {
        return $this->hasMany(CourseConsumptionSession::class);
    }
}

<?php

namespace App\Models\Scopes\Global;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RevenuePeriodAccessScope extends __AbstractScope
{
    /**
     * Teachers may only read periods that produced allocations for them.
     */
    protected function constrain(Builder $builder, Model $model): void
    {
        $teacherId = $this->requireTeacherId($builder);

        if ($teacherId === null) {
            return;
        }

        $builder->whereHas('allocations', function (Builder $query) use ($teacherId): void {
            $query->withoutGlobalScopes()
                ->where('teacher_id', $teacherId);
        });
    }
}

<?php

namespace App\Models\Scopes\Global;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPaymentAccessScope extends __AbstractScope
{
    protected function constrain(Builder $builder, Model $model): void
    {
        $studentId = $this->requireStudentId($builder);

        if ($studentId === null) {
            return;
        }

        $builder->whereHas('subscription', function (Builder $query) use ($studentId): void {
            $query->withoutGlobalScopes()
                ->where('student_id', $studentId);
        });
    }
}

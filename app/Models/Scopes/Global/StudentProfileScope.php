<?php

namespace App\Models\Scopes\Global;

use App\Support\AuthActor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StudentProfileScope extends __AbstractScope
{
    protected function constrain(Builder $builder, Model $model): void
    {
        if (! AuthActor::isStudent()) {
            $this->deny($builder);

            return;
        }

        $builder->where($model->qualifyColumn('user_id'), AuthActor::user()->id);
    }
}

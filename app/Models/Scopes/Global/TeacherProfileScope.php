<?php

namespace App\Models\Scopes\Global;

use App\Support\AuthActor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TeacherProfileScope extends __AbstractScope
{
    protected function constrain(Builder $builder, Model $model): void
    {
        // Students may browse teacher profiles for courses they can access.
        if (AuthActor::isStudent()) {
            return;
        }

        if (! AuthActor::isTeacher()) {
            $this->deny($builder);

            return;
        }

        $builder->where($model->qualifyColumn('user_id'), AuthActor::user()->id);
    }
}

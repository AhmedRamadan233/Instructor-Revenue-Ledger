<?php

namespace App\Models\Scopes\Global;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ManagerProfileScope extends __AbstractScope
{
    /**
     * Manager profiles are invisible to students and teachers.
     */
    protected function constrain(Builder $builder, Model $model): void
    {
        $this->deny($builder);
    }
}

<?php

namespace App\Models\Scopes\Global;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ManagerOnlyScope extends __AbstractScope
{
    /**
     * Only managers (and console/jobs) can read these rows.
     */
    protected function constrain(Builder $builder, Model $model): void
    {
        $this->deny($builder);
    }
}

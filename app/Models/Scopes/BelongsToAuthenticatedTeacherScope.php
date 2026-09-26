<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BelongsToAuthenticatedTeacherScope extends __AbstractScope
{
    public function __construct(protected string $column = 'teacher_id') {}

    protected function constrain(Builder $builder, Model $model): void
    {
        $teacherId = $this->requireTeacherId($builder);

        if ($teacherId === null) {
            return;
        }

        $builder->where($model->qualifyColumn($this->column), $teacherId);
    }
}

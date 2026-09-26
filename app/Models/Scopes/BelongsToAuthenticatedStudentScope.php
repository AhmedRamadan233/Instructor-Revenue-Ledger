<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BelongsToAuthenticatedStudentScope extends __AbstractScope
{
    public function __construct(protected string $column = 'student_id') {}

    protected function constrain(Builder $builder, Model $model): void
    {
        $studentId = $this->requireStudentId($builder);

        if ($studentId === null) {
            return;
        }

        $builder->where($model->qualifyColumn($this->column), $studentId);
    }
}

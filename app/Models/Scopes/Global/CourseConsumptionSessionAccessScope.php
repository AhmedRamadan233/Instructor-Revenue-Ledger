<?php

namespace App\Models\Scopes\Global;

use App\Support\AuthActor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CourseConsumptionSessionAccessScope extends __AbstractScope
{
    protected function constrain(Builder $builder, Model $model): void
    {
        if (AuthActor::isStudent()) {
            $studentId = $this->requireStudentId($builder);

            if ($studentId === null) {
                return;
            }

            $builder->where($model->qualifyColumn('student_id'), $studentId);

            return;
        }

        if (AuthActor::isTeacher()) {
            $teacherId = $this->requireTeacherId($builder);

            if ($teacherId === null) {
                return;
            }

            $builder->whereHas('course', function (Builder $query) use ($teacherId): void {
                $query->withoutGlobalScopes()
                    ->where('teacher_id', $teacherId);
            });

            return;
        }

        $this->deny($builder);
    }
}

<?php

namespace App\Models\Scopes\Global;

use App\Support\AuthActor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class StudentProfileScope extends __AbstractScope
{
    protected function constrain(Builder $builder, Model $model): void
    {
        if (AuthActor::isStudent()) {
            $builder->where($model->qualifyColumn('user_id'), AuthActor::user()->id);

            return;
        }

        if (AuthActor::isTeacher()) {
            $teacherId = $this->requireTeacherId($builder);

            if ($teacherId === null) {
                return;
            }

            $builder->whereExists(function ($query) use ($teacherId, $model): void {
                $query->selectRaw('1')
                    ->from('course_consumption_sessions')
                    ->join('courses', 'courses.id', '=', 'course_consumption_sessions.course_id')
                    ->whereColumn(
                        'course_consumption_sessions.student_id',
                        $model->qualifyColumn('id')
                    )
                    ->where('courses.teacher_id', $teacherId);
            });

            return;
        }

        $this->deny($builder);
    }
}

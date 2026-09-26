<?php

namespace App\Models\Scopes\Global;

use App\Enums\CourseStatus;
use App\Enums\SubscriptionStatus;
use App\Support\AuthActor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CourseAccessScope extends __AbstractScope
{
    protected function constrain(Builder $builder, Model $model): void
    {
        if (AuthActor::isTeacher()) {
            $teacherId = $this->requireTeacherId($builder);

            if ($teacherId === null) {
                return;
            }

            $builder->where($model->qualifyColumn('teacher_id'), $teacherId);

            return;
        }

        if (AuthActor::isStudent()) {
            $studentId = $this->requireStudentId($builder);

            if ($studentId === null) {
                return;
            }

            $builder
                ->where($model->qualifyColumn('status'), CourseStatus::Published->value)
                ->whereExists(function ($query) use ($studentId): void {
                    $query->selectRaw('1')
                        ->from('subscriptions')
                        ->where('subscriptions.student_id', $studentId)
                        ->where('subscriptions.status', SubscriptionStatus::Active->value)
                        ->where(function ($query): void {
                            $query->whereNull('subscriptions.starts_at')
                                ->orWhere('subscriptions.starts_at', '<=', now());
                        })
                        ->where(function ($query): void {
                            $query->whereNull('subscriptions.ends_at')
                                ->orWhere('subscriptions.ends_at', '>=', now());
                        });
                });

            return;
        }

        $this->deny($builder);
    }
}

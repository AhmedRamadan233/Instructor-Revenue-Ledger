<?php

namespace App\Models\Scopes\Global;

use App\Models\User;
use App\Support\AuthActor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

abstract class __AbstractScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $this->isolate($builder, $model, function (Builder $builder, Model $model): void {
            $this->constrain($builder, $model);
        });
    }

    abstract protected function constrain(Builder $builder, Model $model): void;

    protected function isolate(Builder $builder, Model $model, callable $constrain): void
    {
        if (AuthActor::shouldBypassTenantScopes()) {
            return;
        }

        if (AuthActor::denyAll()) {
            $this->deny($builder);

            return;
        }

        /** @var User $user */
        $user = AuthActor::user();

        $constrain($builder, $model, $user);
    }

    protected function deny(Builder $builder): void
    {
        $builder->whereRaw('0 = 1');
    }

    protected function requireStudentId(Builder $builder): ?int
    {
        if (! AuthActor::isStudent()) {
            $this->deny($builder);

            return null;
        }

        $studentId = AuthActor::studentId();

        if ($studentId === null) {
            $this->deny($builder);

            return null;
        }

        return $studentId;
    }

    protected function requireTeacherId(Builder $builder): ?int
    {
        if (! AuthActor::isTeacher()) {
            $this->deny($builder);

            return null;
        }

        $teacherId = AuthActor::teacherId();

        if ($teacherId === null) {
            $this->deny($builder);

            return null;
        }

        return $teacherId;
    }
}

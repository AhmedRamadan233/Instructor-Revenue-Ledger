<?php

namespace App\Repo\Elequent;

use App\Models\Teacher;
use App\Repo\InterFace\TeacherRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class TeacherRepository extends Repository implements TeacherRepositoryInterface
{
    protected Model $model;

    public function __construct(Teacher $model)
    {
        parent::__construct($model);
    }
}

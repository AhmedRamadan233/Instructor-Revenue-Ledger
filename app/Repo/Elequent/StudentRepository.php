<?php

namespace App\Repo\Elequent;

use App\Models\Student;
use App\Repo\InterFace\StudentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class StudentRepository extends Repository implements StudentRepositoryInterface
{
    protected Model $model;

    public function __construct(Student $model)
    {
        parent::__construct($model);
    }
}

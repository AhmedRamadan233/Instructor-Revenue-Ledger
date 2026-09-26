<?php

namespace App\Repo\Elequent;

use App\Models\Course;
use App\Repo\InterFace\CourseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class CourseRepository extends Repository implements CourseRepositoryInterface
{
    protected Model $model;

    public function __construct(Course $model)
    {
        parent::__construct($model);
    }
}

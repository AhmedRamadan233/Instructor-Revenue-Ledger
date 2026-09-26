<?php

namespace App\Repo\Elequent;

use App\Models\CourseConsumptionSession;
use App\Repo\InterFace\CourseConsumptionSessionRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class CourseConsumptionSessionRepository extends Repository implements CourseConsumptionSessionRepositoryInterface
{
    protected Model $model;

    public function __construct(CourseConsumptionSession $model)
    {
        parent::__construct($model);
    }
}

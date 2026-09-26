<?php

namespace App\Repo\Elequent;

use App\Models\Plan;
use App\Repo\InterFace\PlanRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class PlanRepository extends Repository implements PlanRepositoryInterface
{
    protected Model $model;

    public function __construct(Plan $model)
    {
        parent::__construct($model);
    }
}

<?php

namespace App\Repo\Elequent;

use App\Models\PlanOption;
use App\Repo\InterFace\PlanOptionRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class PlanOptionRepository extends Repository implements PlanOptionRepositoryInterface
{
    protected Model $model;

    public function __construct(PlanOption $model)
    {
        parent::__construct($model);
    }
}

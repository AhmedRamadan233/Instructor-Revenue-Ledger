<?php

namespace App\Repo\Elequent;

use App\Models\RevenuePeriod;
use App\Repo\InterFace\RevenuePeriodRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class RevenuePeriodRepository extends Repository implements RevenuePeriodRepositoryInterface
{
    protected Model $model;

    public function __construct(RevenuePeriod $model)
    {
        parent::__construct($model);
    }
}

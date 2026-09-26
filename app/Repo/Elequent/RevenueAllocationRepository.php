<?php

namespace App\Repo\Elequent;

use App\Models\RevenueAllocation;
use App\Repo\InterFace\RevenueAllocationRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class RevenueAllocationRepository extends Repository implements RevenueAllocationRepositoryInterface
{
    protected Model $model;

    public function __construct(RevenueAllocation $model)
    {
        parent::__construct($model);
    }
}

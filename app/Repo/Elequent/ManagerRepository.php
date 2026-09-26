<?php

namespace App\Repo\Elequent;

use App\Models\Manager;
use App\Repo\InterFace\ManagerRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class ManagerRepository extends Repository implements ManagerRepositoryInterface
{
    protected Model $model;

    public function __construct(Manager $model)
    {
        parent::__construct($model);
    }
}

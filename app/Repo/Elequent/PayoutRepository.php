<?php

namespace App\Repo\Elequent;

use App\Models\Payout;
use App\Repo\InterFace\PayoutRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class PayoutRepository extends Repository implements PayoutRepositoryInterface
{
    protected Model $model;

    public function __construct(Payout $model)
    {
        parent::__construct($model);
    }
}

<?php

namespace App\Repo\Elequent;

use App\Models\Subscription;
use App\Repo\InterFace\SubscriptionRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class SubscriptionRepository extends Repository implements SubscriptionRepositoryInterface
{
    protected Model $model;

    public function __construct(Subscription $model)
    {
        parent::__construct($model);
    }
}

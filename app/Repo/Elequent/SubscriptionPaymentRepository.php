<?php

namespace App\Repo\Elequent;

use App\Models\SubscriptionPayment;
use App\Repo\InterFace\SubscriptionPaymentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPaymentRepository extends Repository implements SubscriptionPaymentRepositoryInterface
{
    protected Model $model;

    public function __construct(SubscriptionPayment $model)
    {
        parent::__construct($model);
    }
}

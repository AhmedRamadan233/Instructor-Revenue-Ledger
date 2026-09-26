<?php

namespace App\Repo\Elequent;

use App\Models\User;
use App\Repo\InterFace\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends Repository implements UserRepositoryInterface
{
    protected Model $model;

    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}

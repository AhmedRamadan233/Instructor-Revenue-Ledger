<?php

namespace App\Repo\Elequent;

use App\Models\Setting;
use App\Repo\InterFace\SettingRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class SettingRepository extends Repository implements SettingRepositoryInterface
{
    protected Model $model;

    public function __construct(Setting $model)
    {
        parent::__construct($model);
    }
}

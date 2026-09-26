<?php

namespace App\Repo\Elequent;

use App\Models\TeacherLedgerEntry;
use App\Repo\InterFace\TeacherLedgerEntryRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class TeacherLedgerEntryRepository extends Repository implements TeacherLedgerEntryRepositoryInterface
{
    protected Model $model;

    public function __construct(TeacherLedgerEntry $model)
    {
        parent::__construct($model);
    }
}

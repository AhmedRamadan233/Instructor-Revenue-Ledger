<?php

namespace App\Models;

use App\Enums\SettingType;
use App\Models\Scopes\ManagerOnlyScope;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;

#[ScopedBy([ManagerOnlyScope::class])]
#[Fillable(['key', 'value', 'type'])]
class Setting extends Model
{

    protected function casts(): array
    {
        return [
            'type' => SettingType::class,
        ];
    }
}

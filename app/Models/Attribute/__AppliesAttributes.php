<?php

namespace App\Models\Attribute;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait __AppliesAttributes
{
    /**
     * Cached boolean accessor: related row exists (ignoring global scopes).
     */
    protected function relationExistsAttribute(string $relation): Attribute
    {
        return Attribute::get(
            fn (): bool => $this->{$relation}()->withoutGlobalScopes()->exists()
        )->shouldCache();
    }
}

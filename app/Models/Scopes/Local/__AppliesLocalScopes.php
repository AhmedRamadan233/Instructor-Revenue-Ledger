<?php

namespace App\Models\Scopes\Local;

use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait __AppliesLocalScopes
{
    /**
     * @param  Builder<Model>  $query
     * @param  list<string>  $columns
     * @param  (callable(Builder<Model>, string): void)|null  $extra
     * @return Builder<Model>
     */
    protected function applySearch(
        Builder $query,
        ?string $term,
        array $columns,
        ?callable $extra = null,
    ): Builder {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($term, $columns, $extra): void {
            $model = $query->getModel();

            foreach ($columns as $index => $column) {
                $method = $index === 0 ? 'where' : 'orWhere';
                $query->{$method}($model->qualifyColumn($column), 'like', "%{$term}%");
            }

            if ($extra !== null) {
                $extra($query, $term);
            }
        });
    }

    /**
     * @template T of BackedEnum
     *
     * @param  Builder<Model>  $query
     * @param  class-string<T>  $enumClass
     * @param  T|int|string|null  $value
     * @return Builder<Model>
     */
    protected function applyEnumFilter(
        Builder $query,
        string $column,
        null|int|string|BackedEnum $value,
        string $enumClass,
    ): Builder {
        if ($value === null || $value === '') {
            return $query;
        }

        $enum = $value instanceof BackedEnum
            ? $value
            : $enumClass::tryFrom((int) $value);

        if ($enum === null) {
            return $query;
        }

        return $query->where($query->getModel()->qualifyColumn($column), $enum);
    }
}

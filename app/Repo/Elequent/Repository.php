<?php

namespace App\Repo\Elequent;

use App\Repo\InterFace\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class Repository implements RepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function query(bool $withoutGlobalScopes = false): Builder
    {
        $query = $this->model->newQuery();

        return $withoutGlobalScopes ? $query->withoutGlobalScopes() : $query;
    }

    public function count(array $conditions = [], bool $withoutGlobalScopes = false): int
    {
        return $this->applyConditions($this->query($withoutGlobalScopes), $conditions)->count();
    }

    public function sum(string $column, array $conditions = [], bool $withoutGlobalScopes = false): float
    {
        return (float) $this->applyConditions($this->query($withoutGlobalScopes), $conditions)->sum($column);
    }

    public function getAll(array $columns = ['*'], array $relations = [], bool $withoutGlobalScopes = false): Collection
    {
        return $this->query($withoutGlobalScopes)->with($relations)->get($columns);
    }

    public function getById(
        int|string $modelId,
        array $columns = ['*'],
        array $relations = [],
        bool $withoutGlobalScopes = false,
    ): Model {
        return $this->query($withoutGlobalScopes)->select($columns)->with($relations)->findOrFail($modelId);
    }

    public function find(
        int|string $modelId,
        array $columns = ['*'],
        array $relations = [],
        bool $withoutGlobalScopes = false,
    ): ?Model {
        return $this->query($withoutGlobalScopes)->select($columns)->with($relations)->find($modelId);
    }

    public function findWhere(
        array $conditions,
        array $columns = ['*'],
        array $relations = [],
        bool $withoutGlobalScopes = false,
    ): ?Model {
        return $this->applyConditions(
            $this->query($withoutGlobalScopes)->select($columns)->with($relations),
            $conditions,
        )->first();
    }

    public function getWhere(
        array $conditions = [],
        array $columns = ['*'],
        array $relations = [],
        bool $withoutGlobalScopes = false,
    ): Collection {
        return $this->applyConditions(
            $this->query($withoutGlobalScopes)->select($columns)->with($relations),
            $conditions,
        )->get();
    }

    public function first(
        string $byColumn,
        mixed $value,
        array $columns = ['*'],
        array $relations = [],
        bool $withoutGlobalScopes = false,
    ): ?Model {
        return $this->query($withoutGlobalScopes)
            ->select($columns)
            ->with($relations)
            ->where($byColumn, $value)
            ->first();
    }

    public function create(array $payload): Model
    {
        return $this->model->newQuery()->create($payload)->fresh();
    }

    public function update(int|string $modelId, array $payload, bool $withoutGlobalScopes = false): bool
    {
        return $this->getById($modelId, withoutGlobalScopes: $withoutGlobalScopes)->update($payload);
    }

    public function updateWhere(array $conditions, array $payload, bool $withoutGlobalScopes = false): int
    {
        return $this->applyConditions($this->query($withoutGlobalScopes), $conditions)->update($payload);
    }

    public function delete(int|string $modelId, bool $withoutGlobalScopes = false): bool
    {
        return (bool) $this->getById($modelId, withoutGlobalScopes: $withoutGlobalScopes)->delete();
    }

    public function deleteWhere(array $conditions, bool $withoutGlobalScopes = false): int
    {
        return $this->applyConditions($this->query($withoutGlobalScopes), $conditions)->delete();
    }

    public function updateOrCreate(array $conditions, array $payload): Model
    {
        return $this->query()->updateOrCreate($conditions, $payload);
    }

    public function paginate(
        int $perPage = 10,
        array $relations = [],
        string $orderBy = 'id',
        string $direction = 'desc',
        array $columns = ['*'],
        bool $withoutGlobalScopes = false,
    ): LengthAwarePaginator {
        return $this->query($withoutGlobalScopes)
            ->select($columns)
            ->with($relations)
            ->orderBy($orderBy, $direction)
            ->paginate($perPage);
    }

    public function forTable(
        array $relations = [],
        array $scopes = [],
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        array $allowedSorts = ['created_at'],
        string $defaultSort = 'created_at',
        int $perPage = 10,
        bool $withoutGlobalScopes = false,
        ?callable $modify = null,
    ): LengthAwarePaginator {
        $query = $this->query($withoutGlobalScopes)->with($relations);

        foreach ($scopes as $scope => $parameters) {
            $query->{$scope}(...(array) $parameters);
        }

        if ($modify !== null) {
            $modify($query);
        }

        $column = in_array($sortBy, $allowedSorts, true) ? $sortBy : $defaultSort;
        $direction = $sortDirection === 'asc' ? 'asc' : 'desc';
        $query->orderBy($column, $direction);

        return $query->paginate($perPage);
    }

    public function getWith(
        array $relations = [],
        array $scopes = [],
        array $conditions = [],
        ?string $orderBy = null,
        string $direction = 'asc',
        bool $withoutGlobalScopes = false,
        ?callable $modify = null,
        ?int $limit = null,
    ): Collection {
        $query = $this->applyConditions(
            $this->query($withoutGlobalScopes)->with($relations),
            $conditions,
        );

        foreach ($scopes as $scope => $parameters) {
            $query->{$scope}(...(array) $parameters);
        }

        if ($modify !== null) {
            $modify($query);
        }

        if ($orderBy !== null) {
            $query->orderBy($orderBy, $direction);
        }

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function take(
        int $limit = 5,
        array $relations = [],
        array $columns = ['*'],
        array $conditions = [],
        string $orderBy = 'id',
        string $direction = 'desc',
        bool $withoutGlobalScopes = false,
    ): Collection {
        return $this->applyConditions(
            $this->query($withoutGlobalScopes)->select($columns)->with($relations),
            $conditions,
        )
            ->orderBy($orderBy, $direction)
            ->limit($limit)
            ->get();
    }

    public function lockForUpdateById(int|string $modelId, bool $withoutGlobalScopes = false): Model
    {
        return $this->query($withoutGlobalScopes)
            ->whereKey($modelId)
            ->lockForUpdate()
            ->firstOrFail();
    }

    public function lockForUpdateWhere(array $conditions, bool $withoutGlobalScopes = false): Collection
    {
        return $this->applyConditions($this->query($withoutGlobalScopes), $conditions)
            ->lockForUpdate()
            ->get();
    }

    /**
     * @param  Builder<Model>  $query
     * @param  array<string, mixed>  $conditions
     * @return Builder<Model>
     */
    protected function applyConditions(Builder $query, array $conditions): Builder
    {
        foreach ($conditions as $column => $value) {
            if (is_array($value)) {
                $query->whereIn($column, $value);
            } else {
                $query->where($column, $value);
            }
        }

        return $query;
    }
}

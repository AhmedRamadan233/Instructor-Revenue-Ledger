<?php

namespace App\Repo\InterFace;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    /**
     * @return Builder<Model>
     */
    public function query(bool $withoutGlobalScopes = false): Builder;

    /**
     * @param  array<string, mixed>  $conditions
     */
    public function count(array $conditions = [], bool $withoutGlobalScopes = false): int;

    /**
     * @param  array<string, mixed>  $conditions
     */
    public function sum(string $column, array $conditions = [], bool $withoutGlobalScopes = false): float;

    /**
     * @param  list<string>  $columns
     * @param  array<int|string, mixed>  $relations
     * @return Collection<int, Model>
     */
    public function getAll(array $columns = ['*'], array $relations = [], bool $withoutGlobalScopes = false): Collection;

    /**
     * @param  list<string>  $columns
     * @param  array<int|string, mixed>  $relations
     */
    public function getById(
        int|string $modelId,
        array $columns = ['*'],
        array $relations = [],
        bool $withoutGlobalScopes = false,
    ): Model;

    /**
     * @param  list<string>  $columns
     * @param  array<int|string, mixed>  $relations
     */
    public function find(
        int|string $modelId,
        array $columns = ['*'],
        array $relations = [],
        bool $withoutGlobalScopes = false,
    ): ?Model;

    /**
     * @param  array<string, mixed>  $conditions
     * @param  list<string>  $columns
     * @param  array<int|string, mixed>  $relations
     */
    public function findWhere(
        array $conditions,
        array $columns = ['*'],
        array $relations = [],
        bool $withoutGlobalScopes = false,
    ): ?Model;

    /**
     * @param  array<string, mixed>  $conditions
     * @param  list<string>  $columns
     * @param  array<int|string, mixed>  $relations
     * @return Collection<int, Model>
     */
    public function getWhere(
        array $conditions = [],
        array $columns = ['*'],
        array $relations = [],
        bool $withoutGlobalScopes = false,
    ): Collection;

    /**
     * @param  list<string>  $columns
     * @param  array<int|string, mixed>  $relations
     */
    public function first(
        string $byColumn,
        mixed $value,
        array $columns = ['*'],
        array $relations = [],
        bool $withoutGlobalScopes = false,
    ): ?Model;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function create(array $payload): Model;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(int|string $modelId, array $payload, bool $withoutGlobalScopes = false): bool;

    /**
     * @param  array<string, mixed>  $conditions
     * @param  array<string, mixed>  $payload
     */
    public function updateWhere(array $conditions, array $payload, bool $withoutGlobalScopes = false): int;

    public function delete(int|string $modelId, bool $withoutGlobalScopes = false): bool;

    /**
     * @param  array<string, mixed>  $conditions
     */
    public function deleteWhere(array $conditions, bool $withoutGlobalScopes = false): int;

    /**
     * @param  array<string, mixed>  $conditions
     * @param  array<string, mixed>  $payload
     */
    public function updateOrCreate(array $conditions, array $payload): Model;

    /**
     * @param  array<int|string, mixed>  $relations
     * @param  list<string>  $columns
     */
    public function paginate(
        int $perPage = 10,
        array $relations = [],
        string $orderBy = 'id',
        string $direction = 'desc',
        array $columns = ['*'],
        bool $withoutGlobalScopes = false,
    ): LengthAwarePaginator;

    /**
     * Table listing helper: relations + local scopes + sorting + paginate.
     *
     * @param  array<int|string, mixed>  $relations
     * @param  array<string, mixed>  $scopes  e.g. ['search' => [$term], 'status' => [$status]]
     * @param  list<string>  $allowedSorts
     * @param  (callable(Builder<Model>): void)|null  $modify
     */
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
    ): LengthAwarePaginator;

    /**
     * @param  array<int|string, mixed>  $relations
     * @param  array<string, mixed>  $scopes
     * @param  array<string, mixed>  $conditions
     * @param  (callable(Builder<Model>): void)|null  $modify
     * @return Collection<int, Model>
     */
    public function getWith(
        array $relations = [],
        array $scopes = [],
        array $conditions = [],
        ?string $orderBy = null,
        string $direction = 'asc',
        bool $withoutGlobalScopes = false,
        ?callable $modify = null,
        ?int $limit = null,
    ): Collection;

    /**
     * @param  array<int|string, mixed>  $relations
     * @param  list<string>  $columns
     * @param  array<string, mixed>  $conditions
     * @return Collection<int, Model>
     */
    public function take(
        int $limit = 5,
        array $relations = [],
        array $columns = ['*'],
        array $conditions = [],
        string $orderBy = 'id',
        string $direction = 'desc',
        bool $withoutGlobalScopes = false,
    ): Collection;

    public function lockForUpdateById(int|string $modelId, bool $withoutGlobalScopes = false): Model;

    /**
     * @param  array<string, mixed>  $conditions
     * @return Collection<int, Model>
     */
    public function lockForUpdateWhere(array $conditions, bool $withoutGlobalScopes = false): Collection;
}

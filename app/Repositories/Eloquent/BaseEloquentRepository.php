<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseEloquentRepository implements BaseRepositoryInterface
{
    abstract protected function modelClass(): string;

    protected function searchableFields(): array
    {
        return [];
    }

    protected function defaultSortField(): string
    {
        return 'created_at';
    }

    public function query(): Builder
    {
        return $this->modelClass()::query();
    }

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = $this->query();

        if (($filters['search'] ?? null) && $this->searchableFields() !== []) {
            $search = $filters['search'];
            $operator = $query->getModel()->getConnection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';

            $query->where(function (Builder $builder) use ($search, $operator): void {
                foreach ($this->searchableFields() as $field) {
                    $builder->orWhere($field, $operator, "%{$search}%");
                }
            });
        }

        $sortBy = $filters['sort_by'] ?? $this->defaultSortField();
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $perPage = (int) ($filters['per_page'] ?? 15);

        return $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
    }

    public function findOrFail(string $id): Model
    {
        return $this->query()->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->query()->create($data);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);

        return $model->refresh();
    }

    public function delete(Model $model): void
    {
        $model->delete();
    }
}

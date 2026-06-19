<?php

namespace App\Services;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

abstract class BaseCrudService
{
    public function __construct(
        protected readonly BaseRepositoryInterface $repository
    ) {}

    public function paginate(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($filters);
    }

    public function findOrFail(string $id): Model
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return DB::transaction(fn () => $this->repository->create($data));
    }

    public function update(string $id, array $data): Model
    {
        return DB::transaction(function () use ($id, $data) {
            $model = $this->repository->findOrFail($id);

            return $this->repository->update($model, $data);
        });
    }

    public function delete(string $id): void
    {
        DB::transaction(function () use ($id): void {
            $model = $this->repository->findOrFail($id);
            $this->repository->delete($model);
        });
    }
}

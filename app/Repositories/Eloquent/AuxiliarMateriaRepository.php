<?php

namespace App\Repositories\Eloquent;

use App\Models\AuxiliarMateria;
use App\Repositories\Contracts\AuxiliarMateriaRepositoryInterface;

class AuxiliarMateriaRepository extends BaseEloquentRepository implements AuxiliarMateriaRepositoryInterface
{
    protected function modelClass(): string
    {
        return AuxiliarMateria::class;
    }

    protected function searchableFields(): array
    {
        return ['estado'];
    }

    protected function defaultSortField(): string
    {
        return 'created_at';
    }
}

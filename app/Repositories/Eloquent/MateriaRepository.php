<?php

namespace App\Repositories\Eloquent;

use App\Models\Materia;
use App\Repositories\Contracts\MateriaRepositoryInterface;

class MateriaRepository extends BaseEloquentRepository implements MateriaRepositoryInterface
{
    protected function modelClass(): string
    {
        return Materia::class;
    }

    protected function searchableFields(): array
    {
        return ['codigo', 'nombre', 'descripcion'];
    }

    protected function defaultSortField(): string
    {
        return 'created_at';
    }
}

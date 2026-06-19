<?php

namespace App\Repositories\Eloquent;

use App\Models\SesionAyudantia;
use App\Repositories\Contracts\SesionAyudantiaRepositoryInterface;

class SesionAyudantiaRepository extends BaseEloquentRepository implements SesionAyudantiaRepositoryInterface
{
    protected function modelClass(): string
    {
        return SesionAyudantia::class;
    }

    protected function searchableFields(): array
    {
        return ['aula_ref_id', 'aula_nombre_cache', 'estado'];
    }

    protected function defaultSortField(): string
    {
        return 'created_at';
    }
}

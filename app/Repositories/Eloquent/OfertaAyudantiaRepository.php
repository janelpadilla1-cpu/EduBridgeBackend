<?php

namespace App\Repositories\Eloquent;

use App\Models\OfertaAyudantia;
use App\Repositories\Contracts\OfertaAyudantiaRepositoryInterface;

class OfertaAyudantiaRepository extends BaseEloquentRepository implements OfertaAyudantiaRepositoryInterface
{
    protected function modelClass(): string
    {
        return OfertaAyudantia::class;
    }

    protected function searchableFields(): array
    {
        return ['titulo', 'descripcion', 'estado'];
    }

    protected function defaultSortField(): string
    {
        return 'created_at';
    }
}

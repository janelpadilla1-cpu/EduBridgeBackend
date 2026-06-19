<?php

namespace App\Repositories\Eloquent;

use App\Models\InscripcionAyudantia;
use App\Repositories\Contracts\InscripcionAyudantiaRepositoryInterface;

class InscripcionAyudantiaRepository extends BaseEloquentRepository implements InscripcionAyudantiaRepositoryInterface
{
    protected function modelClass(): string
    {
        return InscripcionAyudantia::class;
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

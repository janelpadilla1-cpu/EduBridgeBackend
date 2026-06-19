<?php

namespace App\Repositories\Eloquent;

use App\Models\PostulacionAuxiliar;
use App\Repositories\Contracts\PostulacionAuxiliarRepositoryInterface;

class PostulacionAuxiliarRepository extends BaseEloquentRepository implements PostulacionAuxiliarRepositoryInterface
{
    protected function modelClass(): string
    {
        return PostulacionAuxiliar::class;
    }

    protected function searchableFields(): array
    {
        return ['motivo', 'experiencia', 'estado'];
    }

    protected function defaultSortField(): string
    {
        return 'created_at';
    }
}

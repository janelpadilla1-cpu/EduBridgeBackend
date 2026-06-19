<?php

namespace App\Repositories\Eloquent;

use App\Models\DisponibilidadAuxiliar;
use App\Repositories\Contracts\DisponibilidadAuxiliarRepositoryInterface;

class DisponibilidadAuxiliarRepository extends BaseEloquentRepository implements DisponibilidadAuxiliarRepositoryInterface
{
    protected function modelClass(): string
    {
        return DisponibilidadAuxiliar::class;
    }

    protected function searchableFields(): array
    {
        return ['dia_semana', 'estado'];
    }

    protected function defaultSortField(): string
    {
        return 'created_at';
    }
}

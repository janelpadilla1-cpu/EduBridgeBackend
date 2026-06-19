<?php

namespace App\Http\Requests;

class ListDisponibilidadAuxiliarRequest extends BaseListRequest
{
    protected function allowedSortFields(): array
    {
        return ['created_at', 'dia_semana', 'hora_inicio', 'estado'];
    }
}

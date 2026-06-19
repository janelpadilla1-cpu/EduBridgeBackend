<?php

namespace App\Http\Requests;

class ListSesionAyudantiaRequest extends BaseListRequest
{
    protected function allowedSortFields(): array
    {
        return ['created_at', 'fecha', 'hora_inicio', 'estado'];
    }
}

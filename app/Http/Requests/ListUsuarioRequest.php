<?php

namespace App\Http\Requests;

class ListUsuarioRequest extends BaseListRequest
{
    protected function allowedSortFields(): array
    {
        return ['created_at', 'nombre_completo', 'correo_institucional', 'estado'];
    }
}

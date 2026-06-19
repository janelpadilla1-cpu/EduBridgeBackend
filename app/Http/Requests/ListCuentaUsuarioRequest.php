<?php

namespace App\Http\Requests;

class ListCuentaUsuarioRequest extends BaseListRequest
{
    protected function allowedSortFields(): array
    {
        return ['created_at', 'estado', 'ultimo_acceso'];
    }
}

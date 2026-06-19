<?php

namespace App\Http\Requests;

class ListRolUsuarioRequest extends BaseListRequest
{
    protected function allowedSortFields(): array
    {
        return ['created_at', 'nombre'];
    }
}

<?php

namespace App\Http\Requests;

class ListMateriaRequest extends BaseListRequest
{
    protected function allowedSortFields(): array
    {
        return ['created_at', 'codigo', 'nombre', 'estado'];
    }
}

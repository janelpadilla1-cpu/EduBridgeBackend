<?php

namespace App\Http\Requests;

class ListAuxiliarMateriaRequest extends BaseListRequest
{
    protected function allowedSortFields(): array
    {
        return ['created_at', 'fecha_asignacion', 'estado'];
    }
}

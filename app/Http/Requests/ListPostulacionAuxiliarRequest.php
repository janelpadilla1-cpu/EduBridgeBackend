<?php

namespace App\Http\Requests;

class ListPostulacionAuxiliarRequest extends BaseListRequest
{
    protected function allowedSortFields(): array
    {
        return ['created_at', 'fecha_postulacion', 'estado'];
    }
}

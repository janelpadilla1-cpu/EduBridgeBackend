<?php

namespace App\Http\Requests;

class ListInscripcionAyudantiaRequest extends BaseListRequest
{
    protected function allowedSortFields(): array
    {
        return ['created_at', 'fecha_inscripcion', 'estado'];
    }
}

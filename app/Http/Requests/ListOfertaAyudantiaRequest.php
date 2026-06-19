<?php

namespace App\Http\Requests;

class ListOfertaAyudantiaRequest extends BaseListRequest
{
    protected function allowedSortFields(): array
    {
        return ['created_at', 'fecha_creacion', 'titulo', 'estado', 'cupo_maximo'];
    }
}

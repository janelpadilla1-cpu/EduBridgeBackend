<?php

namespace App\Http\Requests;

class ListUsuarioRolRequest extends BaseListRequest
{
    protected function allowedSortFields(): array
    {
        return ['created_at'];
    }
}

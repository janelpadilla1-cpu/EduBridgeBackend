<?php

namespace App\Repositories\Eloquent;

use App\Models\UsuarioRol;
use App\Repositories\Contracts\UsuarioRolRepositoryInterface;

class UsuarioRolRepository extends BaseEloquentRepository implements UsuarioRolRepositoryInterface
{
    protected function modelClass(): string
    {
        return UsuarioRol::class;
    }

    protected function searchableFields(): array
    {
        return [];
    }

    protected function defaultSortField(): string
    {
        return 'created_at';
    }
}

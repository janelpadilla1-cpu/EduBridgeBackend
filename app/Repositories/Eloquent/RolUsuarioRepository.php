<?php

namespace App\Repositories\Eloquent;

use App\Models\RolUsuario;
use App\Repositories\Contracts\RolUsuarioRepositoryInterface;

class RolUsuarioRepository extends BaseEloquentRepository implements RolUsuarioRepositoryInterface
{
    protected function modelClass(): string
    {
        return RolUsuario::class;
    }

    protected function searchableFields(): array
    {
        return ['nombre', 'descripcion'];
    }

    protected function defaultSortField(): string
    {
        return 'created_at';
    }
}

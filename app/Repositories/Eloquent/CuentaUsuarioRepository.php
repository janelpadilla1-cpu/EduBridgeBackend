<?php

namespace App\Repositories\Eloquent;

use App\Models\CuentaUsuario;
use App\Repositories\Contracts\CuentaUsuarioRepositoryInterface;

class CuentaUsuarioRepository extends BaseEloquentRepository implements CuentaUsuarioRepositoryInterface
{
    protected function modelClass(): string
    {
        return CuentaUsuario::class;
    }

    protected function searchableFields(): array
    {
        return ['estado'];
    }

    protected function defaultSortField(): string
    {
        return 'created_at';
    }
}

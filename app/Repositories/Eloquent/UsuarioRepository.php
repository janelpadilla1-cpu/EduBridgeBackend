<?php

namespace App\Repositories\Eloquent;

use App\Models\Usuario;
use App\Repositories\Contracts\UsuarioRepositoryInterface;

class UsuarioRepository extends BaseEloquentRepository implements UsuarioRepositoryInterface
{
    protected function modelClass(): string
    {
        return Usuario::class;
    }

    protected function searchableFields(): array
    {
        return ['external_user_ref', 'codigo_universitario', 'correo_institucional', 'nombre_completo'];
    }

    protected function defaultSortField(): string
    {
        return 'created_at';
    }
}

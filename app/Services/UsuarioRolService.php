<?php

namespace App\Services;

use App\Repositories\Contracts\UsuarioRolRepositoryInterface;

class UsuarioRolService extends BaseCrudService
{
    public function __construct(UsuarioRolRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}

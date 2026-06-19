<?php

namespace App\Services;

use App\Repositories\Contracts\UsuarioRepositoryInterface;

class UsuarioService extends BaseCrudService
{
    public function __construct(UsuarioRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}

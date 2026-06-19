<?php

namespace App\Services;

use App\Repositories\Contracts\CuentaUsuarioRepositoryInterface;

class CuentaUsuarioService extends BaseCrudService
{
    public function __construct(CuentaUsuarioRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}

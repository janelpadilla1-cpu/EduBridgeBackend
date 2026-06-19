<?php

namespace App\Services;

use App\Repositories\Contracts\RolUsuarioRepositoryInterface;

class RolUsuarioService extends BaseCrudService
{
    public function __construct(RolUsuarioRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}

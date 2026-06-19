<?php

namespace App\Services;

use App\Repositories\Contracts\MateriaRepositoryInterface;

class MateriaService extends BaseCrudService
{
    public function __construct(MateriaRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}

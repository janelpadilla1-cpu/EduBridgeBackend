<?php

namespace App\Services;

use App\Repositories\Contracts\AuxiliarMateriaRepositoryInterface;

class AuxiliarMateriaService extends BaseCrudService
{
    public function __construct(AuxiliarMateriaRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}

<?php

namespace App\Services;

use App\Repositories\Contracts\DisponibilidadAuxiliarRepositoryInterface;

class DisponibilidadAuxiliarService extends BaseCrudService
{
    public function __construct(DisponibilidadAuxiliarRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}

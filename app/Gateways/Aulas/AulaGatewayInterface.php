<?php

namespace App\Gateways\Aulas;

interface AulaGatewayInterface
{
    public function consultarDisponibilidad(string $aulaRefId, string $fecha, string $horaInicio, string $horaFin): bool;

    public function reservarAula(string $aulaRefId, string $fecha, string $horaInicio, string $horaFin): bool;
}

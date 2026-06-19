<?php

namespace App\Gateways\Aulas;

class FakeAulaGateway implements AulaGatewayInterface
{
    public function consultarDisponibilidad(string $aulaRefId, string $fecha, string $horaInicio, string $horaFin): bool
    {
        return true;
    }

    public function reservarAula(string $aulaRefId, string $fecha, string $horaInicio, string $horaFin): bool
    {
        return true;
    }
}

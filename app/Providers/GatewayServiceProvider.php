<?php

namespace App\Providers;

use App\Gateways\Aulas\AulaGatewayInterface;
use App\Gateways\Aulas\FakeAulaGateway;
use Illuminate\Support\ServiceProvider;

class GatewayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registro de usuarios: 100% interno, sin gateway de directorio de usuarios.
        // Se mantiene solo el gateway fake de aulas para validar/reservar aulas durante desarrollo local.
        $this->app->bind(AulaGatewayInterface::class, FakeAulaGateway::class);
    }
}

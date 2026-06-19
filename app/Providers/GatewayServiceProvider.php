<?php

namespace App\Providers;

use App\Gateways\Aulas\AulaGatewayInterface;
use App\Gateways\Aulas\FakeAulaGateway;
use App\Gateways\Directorio\DirectorioUniversitarioGatewayInterface;
use App\Gateways\Directorio\FakeDirectorioUniversitarioGateway;
use Illuminate\Support\ServiceProvider;

class GatewayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(DirectorioUniversitarioGatewayInterface::class, FakeDirectorioUniversitarioGateway::class);
        $this->app->bind(AulaGatewayInterface::class, FakeAulaGateway::class);
    }
}

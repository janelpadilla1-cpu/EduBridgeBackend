<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registro general de servicios de aplicación.
    }

    public function boot(): void
    {
        // Configuración global del proyecto.
    }
}

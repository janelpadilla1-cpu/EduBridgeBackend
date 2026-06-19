<?php

namespace App\Providers;

use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Repositories\Eloquent\UsuarioRepository;
use App\Repositories\Contracts\CuentaUsuarioRepositoryInterface;
use App\Repositories\Eloquent\CuentaUsuarioRepository;
use App\Repositories\Contracts\RolUsuarioRepositoryInterface;
use App\Repositories\Eloquent\RolUsuarioRepository;
use App\Repositories\Contracts\UsuarioRolRepositoryInterface;
use App\Repositories\Eloquent\UsuarioRolRepository;
use App\Repositories\Contracts\MateriaRepositoryInterface;
use App\Repositories\Eloquent\MateriaRepository;
use App\Repositories\Contracts\OfertaAyudantiaRepositoryInterface;
use App\Repositories\Eloquent\OfertaAyudantiaRepository;
use App\Repositories\Contracts\SesionAyudantiaRepositoryInterface;
use App\Repositories\Eloquent\SesionAyudantiaRepository;
use App\Repositories\Contracts\InscripcionAyudantiaRepositoryInterface;
use App\Repositories\Eloquent\InscripcionAyudantiaRepository;
use App\Repositories\Contracts\PostulacionAuxiliarRepositoryInterface;
use App\Repositories\Eloquent\PostulacionAuxiliarRepository;
use App\Repositories\Contracts\AuxiliarMateriaRepositoryInterface;
use App\Repositories\Eloquent\AuxiliarMateriaRepository;
use App\Repositories\Contracts\DisponibilidadAuxiliarRepositoryInterface;
use App\Repositories\Eloquent\DisponibilidadAuxiliarRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UsuarioRepositoryInterface::class, UsuarioRepository::class);
        $this->app->bind(CuentaUsuarioRepositoryInterface::class, CuentaUsuarioRepository::class);
        $this->app->bind(RolUsuarioRepositoryInterface::class, RolUsuarioRepository::class);
        $this->app->bind(UsuarioRolRepositoryInterface::class, UsuarioRolRepository::class);
        $this->app->bind(MateriaRepositoryInterface::class, MateriaRepository::class);
        $this->app->bind(OfertaAyudantiaRepositoryInterface::class, OfertaAyudantiaRepository::class);
        $this->app->bind(SesionAyudantiaRepositoryInterface::class, SesionAyudantiaRepository::class);
        $this->app->bind(InscripcionAyudantiaRepositoryInterface::class, InscripcionAyudantiaRepository::class);
        $this->app->bind(PostulacionAuxiliarRepositoryInterface::class, PostulacionAuxiliarRepository::class);
        $this->app->bind(AuxiliarMateriaRepositoryInterface::class, AuxiliarMateriaRepository::class);
        $this->app->bind(DisponibilidadAuxiliarRepositoryInterface::class, DisponibilidadAuxiliarRepository::class);
    }
}

<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CatalogoController;
use App\Http\Controllers\Api\V1\UsuarioController;
use App\Http\Controllers\Api\V1\CuentaUsuarioController;
use App\Http\Controllers\Api\V1\RolUsuarioController;
use App\Http\Controllers\Api\V1\UsuarioRolController;
use App\Http\Controllers\Api\V1\MateriaController;
use App\Http\Controllers\Api\V1\OfertaAyudantiaController;
use App\Http\Controllers\Api\V1\SesionAyudantiaController;
use App\Http\Controllers\Api\V1\InscripcionAyudantiaController;
use App\Http\Controllers\Api\V1\PostulacionAuxiliarController;
use App\Http\Controllers\Api\V1\AuxiliarMateriaController;
use App\Http\Controllers\Api\V1\DisponibilidadAuxiliarController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('catalogos/estados', [CatalogoController::class, 'estados']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::apiResource('usuarios', UsuarioController::class);
        Route::apiResource('cuentas-usuario', CuentaUsuarioController::class);
        Route::apiResource('roles-usuario', RolUsuarioController::class);
        Route::apiResource('usuarios-roles', UsuarioRolController::class);
        Route::apiResource('materias', MateriaController::class);
        Route::apiResource('ofertas-ayudantia', OfertaAyudantiaController::class);
        Route::apiResource('sesiones-ayudantia', SesionAyudantiaController::class);
        Route::apiResource('inscripciones-ayudantia', InscripcionAyudantiaController::class);
        Route::apiResource('postulaciones-auxiliar', PostulacionAuxiliarController::class);
        Route::apiResource('auxiliares-materia', AuxiliarMateriaController::class);
        Route::apiResource('disponibilidad-auxiliar', DisponibilidadAuxiliarController::class);

        Route::post('ofertas-ayudantia/{id}/publicar', [OfertaAyudantiaController::class, 'publicar']);
        Route::post('ofertas-ayudantia/{id}/cerrar', [OfertaAyudantiaController::class, 'cerrar']);
        Route::post('ofertas-ayudantia/{id}/cancelar', [OfertaAyudantiaController::class, 'cancelar']);

        Route::post('sesiones-ayudantia/{id}/iniciar', [SesionAyudantiaController::class, 'iniciar']);
        Route::post('sesiones-ayudantia/{id}/finalizar', [SesionAyudantiaController::class, 'finalizar']);
        Route::post('sesiones-ayudantia/{id}/cancelar', [SesionAyudantiaController::class, 'cancelar']);

        Route::post('inscripciones-ayudantia/{id}/cancelar', [InscripcionAyudantiaController::class, 'cancelar']);
        Route::patch('inscripciones-ayudantia/{id}/asistencia', [InscripcionAyudantiaController::class, 'registrarAsistencia']);

        Route::post('postulaciones-auxiliar/{id}/aprobar', [PostulacionAuxiliarController::class, 'aprobar']);
        Route::post('postulaciones-auxiliar/{id}/rechazar', [PostulacionAuxiliarController::class, 'rechazar']);
        Route::post('postulaciones-auxiliar/{id}/cancelar', [PostulacionAuxiliarController::class, 'cancelar']);
    });
});

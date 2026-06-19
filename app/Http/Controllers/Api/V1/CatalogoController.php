<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CatalogoController extends Controller
{
    public function estados(): JsonResponse
    {
        return response()->json([
            'usuario' => ['ACTIVO', 'INACTIVO'],
            'cuenta_usuario' => ['ACTIVA', 'INACTIVA', 'BLOQUEADA'],
            'materia' => ['ACTIVA', 'INACTIVA'],
            'oferta_ayudantia' => ['BORRADOR', 'PUBLICADA', 'CERRADA', 'CANCELADA'],
            'sesion_ayudantia' => ['PROGRAMADA', 'EN_CURSO', 'FINALIZADA', 'CANCELADA'],
            'inscripcion_ayudantia' => ['INSCRITO', 'EN_ESPERA', 'CANCELADO', 'ASISTIO', 'NO_ASISTIO'],
            'postulacion_auxiliar' => ['PENDIENTE', 'APROBADA', 'RECHAZADA', 'CANCELADA'],
            'auxiliar_materia' => ['ACTIVO', 'INACTIVO'],
            'disponibilidad_auxiliar' => ['ACTIVA', 'INACTIVA'],
        ]);
    }
}

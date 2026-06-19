<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InscripcionAyudantiaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'usuario_id' => $this->usuario_id,
            'sesion_ayudantia_id' => $this->sesion_ayudantia_id,
            'estado' => $this->estado,
            'fecha_inscripcion' => optional($this->fecha_inscripcion)->toISOString(),
            'asistencia' => $this->asistencia,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
            'usuario' => $this->whenLoaded('usuario'),
            'sesionAyudantia' => $this->whenLoaded('sesionAyudantia'),
        ];
    }
}

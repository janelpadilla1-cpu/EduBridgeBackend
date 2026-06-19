<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'external_user_ref' => $this->external_user_ref,
            'codigo_universitario' => $this->codigo_universitario,
            'correo_institucional' => $this->correo_institucional,
            'nombre_completo' => $this->nombre_completo,
            'estado' => $this->estado,
            'fecha_registro' => optional($this->fecha_registro)->toISOString(),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
            'cuenta' => $this->whenLoaded('cuenta'),
            'roles' => $this->whenLoaded('roles'),
            'inscripciones' => $this->whenLoaded('inscripciones'),
            'postulacionesAuxiliar' => $this->whenLoaded('postulacionesAuxiliar'),
            'auxiliaresMateria' => $this->whenLoaded('auxiliaresMateria'),
            'disponibilidades' => $this->whenLoaded('disponibilidades'),
            'sesionesComoAuxiliar' => $this->whenLoaded('sesionesComoAuxiliar'),
        ];
    }
}

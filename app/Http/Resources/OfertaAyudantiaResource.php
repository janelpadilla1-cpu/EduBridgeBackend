<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfertaAyudantiaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'materia_id' => $this->materia_id,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'cupo_maximo' => $this->cupo_maximo,
            'estado' => $this->estado,
            'fecha_creacion' => optional($this->fecha_creacion)->toISOString(),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
            'materia' => $this->whenLoaded('materia'),
            'sesiones' => $this->whenLoaded('sesiones'),
        ];
    }
}

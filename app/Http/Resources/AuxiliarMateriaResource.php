<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuxiliarMateriaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'usuario_id' => $this->usuario_id,
            'materia_id' => $this->materia_id,
            'estado' => $this->estado,
            'fecha_asignacion' => optional($this->fecha_asignacion)->toISOString(),
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
            'usuario' => $this->whenLoaded('usuario'),
            'materia' => $this->whenLoaded('materia'),
        ];
    }
}

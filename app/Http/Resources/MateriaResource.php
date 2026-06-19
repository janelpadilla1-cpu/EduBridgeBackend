<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MateriaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
            'ofertasAyudantia' => $this->whenLoaded('ofertasAyudantia'),
            'postulacionesAuxiliar' => $this->whenLoaded('postulacionesAuxiliar'),
            'auxiliaresMateria' => $this->whenLoaded('auxiliaresMateria'),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SesionAyudantiaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'oferta_ayudantia_id' => $this->oferta_ayudantia_id,
            'auxiliar_id' => $this->auxiliar_id,
            'fecha' => optional($this->fecha)->toISOString(),
            'hora_inicio' => $this->hora_inicio,
            'hora_fin' => $this->hora_fin,
            'aula_ref_id' => $this->aula_ref_id,
            'aula_nombre_cache' => $this->aula_nombre_cache,
            'estado' => $this->estado,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
            'ofertaAyudantia' => $this->whenLoaded('ofertaAyudantia'),
            'auxiliar' => $this->whenLoaded('auxiliar'),
            'inscripciones' => $this->whenLoaded('inscripciones'),
        ];
    }
}

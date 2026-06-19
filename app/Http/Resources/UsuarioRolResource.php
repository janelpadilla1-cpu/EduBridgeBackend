<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioRolResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'usuario_id' => $this->usuario_id,
            'rol_id' => $this->rol_id,
            'created_at' => optional($this->created_at)->toISOString(),
            'usuario' => $this->whenLoaded('usuario'),
            'rol' => $this->whenLoaded('rol'),
        ];
    }
}

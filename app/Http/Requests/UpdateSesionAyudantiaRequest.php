<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSesionAyudantiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'oferta_ayudantia_id' => ['sometimes','uuid','exists:ofertas_ayudantia,id'],
            'auxiliar_id' => ['nullable','uuid','exists:usuarios,id'],
            'fecha' => ['sometimes','date'],
            'hora_inicio' => ['sometimes','date_format:H:i'],
            'hora_fin' => ['sometimes','date_format:H:i','after:hora_inicio'],
            'aula_ref_id' => ['sometimes','string','max:100'],
            'aula_nombre_cache' => ['nullable','string','max:150'],
            'estado' => ['sometimes','string','max:30'],
        ];
    }
}

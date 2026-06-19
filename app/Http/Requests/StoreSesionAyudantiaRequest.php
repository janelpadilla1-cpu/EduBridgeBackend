<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSesionAyudantiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'oferta_ayudantia_id' => ['required','uuid','exists:ofertas_ayudantia,id'],
            'auxiliar_id' => ['nullable','uuid','exists:usuarios,id'],
            'fecha' => ['required','date'],
            'hora_inicio' => ['required','date_format:H:i'],
            'hora_fin' => ['required','date_format:H:i','after:hora_inicio'],
            'aula_ref_id' => ['required','string','max:100'],
            'aula_nombre_cache' => ['nullable','string','max:150'],
            'estado' => ['sometimes','string','max:30'],
        ];
    }
}

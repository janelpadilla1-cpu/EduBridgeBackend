<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfertaAyudantiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'materia_id' => ['sometimes','uuid','exists:materias,id'],
            'titulo' => ['sometimes','string','max:200'],
            'descripcion' => ['nullable','string'],
            'cupo_maximo' => ['sometimes','integer','min:0'],
            'estado' => ['sometimes','string','max:30'],
        ];
    }
}

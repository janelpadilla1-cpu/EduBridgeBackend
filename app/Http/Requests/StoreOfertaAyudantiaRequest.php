<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfertaAyudantiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'materia_id' => ['required','uuid','exists:materias,id'],
            'titulo' => ['required','string','max:200'],
            'descripcion' => ['nullable','string'],
            'cupo_maximo' => ['required','integer','min:0'],
            'estado' => ['sometimes','string','max:30'],
        ];
    }
}

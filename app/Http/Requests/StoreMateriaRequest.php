<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMateriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required','string','max:50','unique:materias,codigo'],
            'nombre' => ['required','string','max:150'],
            'descripcion' => ['nullable','string'],
            'estado' => ['sometimes','string','max:30'],
        ];
    }
}

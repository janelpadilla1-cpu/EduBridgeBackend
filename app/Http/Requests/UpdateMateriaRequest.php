<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMateriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['sometimes','string','max:50'],
            'nombre' => ['sometimes','string','max:150'],
            'descripcion' => ['nullable','string'],
            'estado' => ['sometimes','string','max:30'],
        ];
    }
}

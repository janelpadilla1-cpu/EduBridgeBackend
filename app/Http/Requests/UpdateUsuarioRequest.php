<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'external_user_ref' => ['sometimes','string','max:100'],
            'codigo_universitario' => ['nullable','string','max:50'],
            'correo_institucional' => ['sometimes','email','max:150'],
            'nombre_completo' => ['sometimes','string','max:200'],
            'estado' => ['sometimes','string','max:30'],
        ];
    }
}

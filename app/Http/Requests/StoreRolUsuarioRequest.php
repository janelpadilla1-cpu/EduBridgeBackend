<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRolUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required','string','max:50','unique:roles_usuario,nombre'],
            'descripcion' => ['nullable','string'],
        ];
    }
}

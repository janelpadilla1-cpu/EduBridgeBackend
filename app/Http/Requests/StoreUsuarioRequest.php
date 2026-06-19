<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'external_user_ref' => ['required','string','max:100','unique:usuarios,external_user_ref'],
            'codigo_universitario' => ['nullable','string','max:50','unique:usuarios,codigo_universitario'],
            'correo_institucional' => ['required','email','max:150','unique:usuarios,correo_institucional'],
            'nombre_completo' => ['required','string','max:200'],
            'estado' => ['sometimes','string','max:30'],
        ];
    }
}

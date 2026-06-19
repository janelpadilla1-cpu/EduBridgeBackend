<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCuentaUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usuario_id' => ['sometimes','uuid','exists:usuarios,id'],
            'password_hash' => ['sometimes','string','max:255'],
            'estado' => ['sometimes','string','max:30'],
            'ultimo_acceso' => ['nullable','date'],
        ];
    }
}

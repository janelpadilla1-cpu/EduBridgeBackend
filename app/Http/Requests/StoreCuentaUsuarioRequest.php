<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCuentaUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usuario_id' => ['required','uuid','exists:usuarios,id','unique:cuentas_usuario,usuario_id'],
            'password_hash' => ['required','string','max:255'],
            'estado' => ['sometimes','string','max:30'],
            'ultimo_acceso' => ['nullable','date'],
        ];
    }
}

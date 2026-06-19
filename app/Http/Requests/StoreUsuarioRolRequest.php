<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usuario_id' => ['required','uuid','exists:usuarios,id'],
            'rol_id' => ['required','uuid','exists:roles_usuario,id'],
        ];
    }
}

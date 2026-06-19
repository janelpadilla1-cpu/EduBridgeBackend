<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUsuarioRolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usuario_id' => ['sometimes','uuid','exists:usuarios,id'],
            'rol_id' => ['sometimes','uuid','exists:roles_usuario,id'],
        ];
    }
}

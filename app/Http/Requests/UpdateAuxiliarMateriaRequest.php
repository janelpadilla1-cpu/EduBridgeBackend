<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAuxiliarMateriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usuario_id' => ['sometimes','uuid','exists:usuarios,id'],
            'materia_id' => ['sometimes','uuid','exists:materias,id'],
            'estado' => ['sometimes','string','max:30'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostulacionAuxiliarRequest extends FormRequest
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
            'motivo' => ['nullable','string'],
            'experiencia' => ['nullable','string'],
        ];
    }
}

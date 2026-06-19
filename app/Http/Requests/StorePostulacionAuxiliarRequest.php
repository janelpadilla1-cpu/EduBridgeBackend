<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostulacionAuxiliarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usuario_id' => ['required','uuid','exists:usuarios,id'],
            'materia_id' => ['required','uuid','exists:materias,id'],
            'motivo' => ['nullable','string'],
            'experiencia' => ['nullable','string'],
        ];
    }
}

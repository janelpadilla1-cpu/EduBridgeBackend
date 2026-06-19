<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInscripcionAyudantiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usuario_id' => ['required','uuid','exists:usuarios,id'],
            'sesion_ayudantia_id' => ['required','uuid','exists:sesiones_ayudantia,id'],
        ];
    }
}

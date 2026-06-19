<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDisponibilidadAuxiliarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usuario_id' => ['sometimes','uuid','exists:usuarios,id'],
            'dia_semana' => ['sometimes','string','max:20'],
            'hora_inicio' => ['sometimes','date_format:H:i'],
            'hora_fin' => ['sometimes','date_format:H:i','after:hora_inicio'],
            'estado' => ['sometimes','string','max:30'],
        ];
    }
}

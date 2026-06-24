<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $correo = $this->filled('correo_institucional')
            ? Str::lower(trim((string) $this->input('correo_institucional')))
            : null;

        $this->merge([
            'correo_institucional' => $correo,
            'external_user_ref' => $this->filled('external_user_ref')
                ? trim((string) $this->input('external_user_ref'))
                : $correo,
            'codigo_universitario' => $this->filled('codigo_universitario')
                ? Str::upper(trim((string) $this->input('codigo_universitario')))
                : null,
            'nombre_completo' => $this->filled('nombre_completo')
                ? trim((string) $this->input('nombre_completo'))
                : null,
            'rol' => Str::upper(trim((string) $this->input('rol'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'external_user_ref' => ['required', 'string', 'max:100', 'unique:usuarios,external_user_ref'],
            'codigo_universitario' => ['nullable', 'string', 'max:50', 'unique:usuarios,codigo_universitario'],
            'correo_institucional' => ['required', 'email', 'max:150', 'unique:usuarios,correo_institucional'],
            'nombre_completo' => ['required', 'string', 'max:200'],
            'rol' => ['required', 'string', Rule::in(['ESTUDIANTE', 'AUXILIAR', 'COORDINADOR', 'ADMINISTRADOR'])],
            'password' => ['required', 'string', 'min:8', 'max:100', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'rol.in' => 'El rol debe ser ESTUDIANTE, AUXILIAR, COORDINADOR o ADMINISTRADOR.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->filled('correo_institucional')) {
            $data['correo_institucional'] = Str::lower(trim((string) $this->input('correo_institucional')));
        }

        if ($this->filled('external_user_ref')) {
            $data['external_user_ref'] = trim((string) $this->input('external_user_ref'));
        }

        if ($this->filled('codigo_universitario')) {
            $data['codigo_universitario'] = Str::upper(trim((string) $this->input('codigo_universitario')));
        }

        if ($this->filled('nombre_completo')) {
            $data['nombre_completo'] = trim((string) $this->input('nombre_completo'));
        }

        if ($data !== []) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $id = $this->route('usuario') ?? $this->route('id');

        return [
            'external_user_ref' => ['sometimes', 'string', 'max:100', Rule::unique('usuarios', 'external_user_ref')->ignore($id)],
            'codigo_universitario' => ['nullable', 'string', 'max:50', Rule::unique('usuarios', 'codigo_universitario')->ignore($id)],
            'correo_institucional' => ['sometimes', 'email', 'max:150', Rule::unique('usuarios', 'correo_institucional')->ignore($id)],
            'nombre_completo' => ['sometimes', 'string', 'max:200'],
            'estado' => ['sometimes', 'string', 'max:30'],
        ];
    }
}

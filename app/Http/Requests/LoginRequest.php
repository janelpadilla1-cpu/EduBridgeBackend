<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('correo_institucional')) {
            $this->merge([
                'correo_institucional' => Str::lower(trim((string) $this->input('correo_institucional'))),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'correo_institucional' => ['required', 'email', 'max:150'],
            'password' => ['required', 'string', 'max:100'],
        ];
    }
}

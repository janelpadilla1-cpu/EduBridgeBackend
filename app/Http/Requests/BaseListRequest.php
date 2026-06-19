<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class BaseListRequest extends FormRequest
{
    abstract protected function allowedSortFields(): array;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:150'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'sort_by' => ['nullable', 'string', Rule::in($this->allowedSortFields())],
            'sort_order' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
        ];
    }
}

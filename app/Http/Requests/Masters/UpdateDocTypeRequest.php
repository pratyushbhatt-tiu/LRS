<?php

namespace App\Http\Requests\Masters;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('doc_types', 'code')->ignore($this->route('doc_type'))],
            'name' => ['required', 'string', 'max:255'],
            'active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return ['code' => 'document type code', 'name' => 'document type name', 'active' => 'status'];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['active' => $this->has('active') ? true : false]);
    }
}

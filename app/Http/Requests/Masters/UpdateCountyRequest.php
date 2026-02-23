<?php

namespace App\Http\Requests\Masters;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCountyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'state_id' => ['required', 'exists:states,id'],
            'code' => ['required', 'string', 'max:50', Rule::unique('counties', 'code')->ignore($this->route('county'))],
            'name' => ['required', 'string', 'max:255'],
            'active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return ['state_id' => 'state', 'code' => 'county code', 'name' => 'county name', 'active' => 'status'];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['active' => $this->has('active') ? true : false]);
    }
}

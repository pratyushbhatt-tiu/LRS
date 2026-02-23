<?php

namespace App\Http\Requests\Masters;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'county_id' => ['required', 'exists:counties,id'],
            'code' => ['required', 'string', 'max:50', Rule::unique('cities', 'code')->ignore($this->route('city'))],
            'name' => ['required', 'string', 'max:255'],
            'active' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return ['county_id' => 'county', 'code' => 'city code', 'name' => 'city name', 'active' => 'status'];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['active' => $this->has('active') ? true : false]);
    }
}

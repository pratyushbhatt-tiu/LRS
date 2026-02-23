<?php

namespace App\Http\Requests\Masters;

use Illuminate\Foundation\Http\FormRequest;

class StoreCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'county_id' => ['required', 'exists:counties,id'],
            'code' => ['required', 'string', 'max:50', 'unique:cities,code'],
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

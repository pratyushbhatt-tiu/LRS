<?php

namespace App\Http\Requests\Masters;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeeRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rule_name' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'integer', 'min:0'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'doc_type_id' => ['nullable', 'exists:doc_types,id'],
            'state_id' => ['nullable', 'exists:states,id'],
            'county_id' => ['nullable', 'exists:counties,id'],
            'base_fee' => ['required', 'numeric', 'min:0'],
            'per_page_fee' => ['nullable', 'numeric', 'min:0'],
            'minimum_fee' => ['nullable', 'numeric', 'min:0'],
            'maximum_fee' => ['nullable', 'numeric', 'min:0'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->has('active') ? true : false,
            'client_id' => $this->client_id ?: null,
            'doc_type_id' => $this->doc_type_id ?: null,
            'state_id' => $this->state_id ?: null,
            'county_id' => $this->county_id ?: null,
            'per_page_fee' => $this->per_page_fee ?: null,
            'minimum_fee' => $this->minimum_fee ?: null,
            'maximum_fee' => $this->maximum_fee ?: null,
            'effective_to' => $this->effective_to ?: null,
        ]);
    }
}

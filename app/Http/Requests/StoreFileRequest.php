<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Controlled by Policy/Middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'doc_type_id' => ['required', 'exists:doc_types,id'],
            'recording_purpose_id' => ['required', 'exists:recording_purposes,id'],
            'state_id' => ['required', 'exists:states,id'],
            'county_id' => ['required', 'exists:counties,id'],
            'received_date' => ['required', 'date'],
            'page_count' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'file_no' => 'file number',
            'client_id' => 'client',
            'doc_type_id' => 'document type',
            'recording_purpose_id' => 'recording purpose',
            'state_id' => 'state',
            'county_id' => 'county',
        ];
    }
}

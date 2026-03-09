<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for validating the CSV bulk import upload.
 */
class StoreImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Access is controlled by the route middleware (permission:files.create).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for the CSV upload.
     */
    public function rules(): array
    {
        return [
            // Must be a file, CSV or plain text, max 2MB (2048 KB)
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ];
    }

    /**
     * Human-readable attribute names for error messages.
     */
    public function attributes(): array
    {
        return [
            'csv_file' => 'CSV file',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'csv_file.mimes' => 'The file must be a valid CSV file (.csv or .txt).',
            'csv_file.max' => 'The CSV file must not exceed 2MB.',
            'csv_file.required' => 'Please choose a CSV file to upload.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\DataImportConfigurationTrait;

class DataImportRequest extends FormRequest
{
    use DataImportConfigurationTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Not logged user cannot import
        if (!auth()->check()) {
            return false;
        }

        $importTypeKey = $this->input('import_type_key');
        if (!$importTypeKey) {
            // we allow missing import_type_key here
            // import_type_key rule will handle the validation error
            return true;
        }

        $importConfig = $this->getImportTypeConfiguration($importTypeKey);
        if (!$importConfig || !isset($importConfig['permission_required'])) {
            // If something is wrong with config, deny access
            return false;
        }

        $requiredPermission = $importConfig['permission_required'];

        return auth()->user()->can($requiredPermission);
    }

    /**
     * Failed auth response
     *
     * @return void
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            redirect()->back()->withInput()->with(
                "error",
                "You do not have the required permission to perform this import."
            )
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $importTypeKey = $this->input('import_type_key');
        $keys = $this->getFileKeysByImportType($importTypeKey);

        $rules = [
            'import_type_key' => ['required', 'string'],
        ];

        // lists of file input names
        $fileInputs = collect($keys)
            ->map(fn($key) => "import_file_{$key}");

        foreach ($fileInputs as $input) {
            // All inputs except the current one
            $others = $fileInputs->filter(fn($v) => $v !== $input)->implode(',');

            // Dynamic validation for each file input
            $rules[$input] = [
                'file',
                'mimes:xlsx,csv',
            ];

            // If there is at least one more file field — add required_without
            $rules[$input][] = (empty($others)) ? 'required' : "required_without:{$others}";
        }

        return $rules;
    }

    /**
     * Custom messages for validation errors
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'import_type_key.required' => 'Please select an import type before proceeding.',
        ];
    }

    /**
     * Prepare the validator for adding complex validation logic.
     * Here we check for the presence of required headers.
     * 
     * @param Validator $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Fetching Configuration
            $importConfig = $this->getImportTypeConfiguration($this->input('import_type_key'));
            if (!$importConfig) {
                $validator->errors()->add('import_type_key', 'Data import configuration is missing.');
                return;
            }

            $fileKeys = $this->getFileKeysByImportType($this->input('import_type_key'));
            if (empty($fileKeys)) {
                $validator->errors()->add('import_type_key', 'Data import files configuration is missing.');
                return;
            }
        });
    }
}

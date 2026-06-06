<?php

namespace App\Http\Requests\Report;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reason' => 'required|in:spam,inappropriate,harassment,misinformation,other',
            'additional_info' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'A reason for the report is required.',
            'reason.in' => 'Reason must be one of: spam, inappropriate, harassment, misinformation, other.',
            'additional_info.string' => 'Additional info must be a string.',
            'additional_info.max' => 'Additional info must not exceed 1000 characters.',
        ];
    }
}

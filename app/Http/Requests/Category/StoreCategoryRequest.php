<?php

namespace App\Http\Requests\Category;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'category_name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
        ];
    }

    public function messages(): array
    {
        return [
            'category_name.required' => 'Category name is required.',
            'category_name.string' => 'Category name must be a string.',
            'category_name.max' => 'Category name must not exceed 255 characters.',
            'slug.required' => 'Slug is required.',
            'slug.string' => 'Slug must be a string.',
            'slug.max' => 'Slug must not exceed 255 characters.',
            'slug.unique' => 'Slug must be unique. The provided slug already exists.',
        ];
    }
}

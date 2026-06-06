<?php

namespace App\Http\Requests\Post;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
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
            'post_name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category_id' => 'sometimes|exists:categories,id',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'post_name.string' => 'Post name must be a string.',
            'post_name.max' => 'Post name must not exceed 255 characters.',
            'description.string' => 'Description must be a string.',
            'category_id.exists' => 'The selected category does not exist.',
            'banner_image.image' => 'Banner must be an image.',
            'banner_image.mimes' => 'Banner must be a file of type: jpeg, png, jpg, webp.',
            'banner_image.max' => 'Banner may not be greater than 2048 kilobytes.',
            'images.array' => 'Images must be an array.',
            'images.max' => 'Images may not have more than 10 items.',
            'images.*.image' => 'Each additional image must be an image.',
            'images.*.mimes' => 'Each additional image must be a file of type: jpeg, png, jpg, webp.',
            'images.*.max' => 'Each additional image may not be greater than 2048 kilobytes.',
        ];
    }
}

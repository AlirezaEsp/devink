<?php

namespace App\Features\Article\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                'max:255'
            ],
            'slug' => [
                'required',
                'string',
                'unique:articles,slug'
            ],
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id'
            ],
            'body' => [
                'required',
                'string',
            ],
            'tags' => [
                'sometimes',
                'array',
                'max:5'
            ],
            'tags.*' => [
                'integer',
                'distinct',
                'exists:tags,id'
            ],
        ];
    }
}

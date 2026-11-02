<?php

namespace App\Features\Article\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
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
        $articleId = $this->route('id');

        return [
            'title' => [
                'sometimes',
                'string',
                'max:255'
            ],
            'slug' => [
                'sometimes',
                'string',
                Rule::unique('articles', 'slug')->ignore($articleId)
            ],
            'body' => [
                'sometimes',
                'string',
            ]
        ];
    }
}

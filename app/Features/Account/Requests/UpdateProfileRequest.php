<?php

namespace App\Features\Account\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'username' => [
                'required',
                'string',
                'max:255',
                'unique:profiles,username'
            ],
            'full_name' => [
                'required',
                'string',
                'max:255'
            ],
            'bio' => [
                'nullable',
                'string'
            ],
            'avatar' => [
                'nullable',
                'string'
            ]
        ];
    }
}

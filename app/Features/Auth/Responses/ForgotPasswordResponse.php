<?php

namespace App\Features\Auth\Responses;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * ForgotPasswordResponse
 * 
 * @property string $resource
 */
class ForgotPasswordResponse extends JsonResource
{    
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => __($this->resource)
        ];
    }
}


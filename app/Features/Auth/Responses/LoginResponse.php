<?php

namespace App\Features\Auth\Responses;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Features\Auth\Resources\UserResource;


/**
 * LoginResponse
 * 
 * Performs logged in user and token serilization for responsing
 * 
 * @mixin UserResource
 */
class LoginResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => 'User logged in successfully.',
            'user' => new UserResource($this->resource['user']),
            'token' => $this->resource["token"]
        ];
    }
}

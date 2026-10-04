<?php

namespace App\Features\Account\Responses;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\JsonResponse;
use App\Features\Account\Resources\UserDetailedResource;


/**
 * RegisterResponse
 * 
 * Performs registered user model serilization for responsing
 * 
 * @mixin UserDetailedResource
 */
class RegisterResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => 'User registered successfully. Please verify your email.',
            'user' => new UserDetailedResource($this->resource)
        ];
    }

    public function withResponse(Request $request, JsonResponse $response) {
        $response->setStatusCode(201);
    }
}

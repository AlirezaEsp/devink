<?php

namespace App\Features\Auth\Responses;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Features\Auth\Resources\UserResource;


/**
 * LogoutResponse
 * 
 * Performs logged out user model serilization for responsing
 * 
 * @mixin UserResource
 */
class LogoutResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => 'User logged out successfully.',
            'user' => new UserResource($this->resource['user'])
        ];
    }
}

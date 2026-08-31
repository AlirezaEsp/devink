<?php

namespace App\Features\Auth\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Features\Auth\Models\User;

/**
 * LogoutResponse
 * 
 * Performs logged out user model serilization for responsing
 * 
 * @mixin User
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
            'user' => [
                'id' => $this->resource->id,
                'email' => $this->resource->email,
            ],
            'message' => 'Logged out successfully.'
        ];
    }
}

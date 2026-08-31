<?php

namespace App\Features\Auth\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Features\Auth\Models\User;


/**
 * UserResource
 * 
 * Performs registered user model serilization for responsing
 * 
 * @mixin User
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
            'user' => [
                'id' => $this->resource->id,
                'email' => $this->resource->email,
                'created_at' => $this->resource->created_at,
            ]
        ];
    }
}

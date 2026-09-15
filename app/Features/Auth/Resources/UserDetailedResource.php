<?php

namespace App\Features\Auth\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Features\Auth\Models\User;

/**
 * UserDetailedResource
 * 
 * Returns user model serilization for responsing with detailed information
 * 
 * @mixin User
 */
class UserDetailedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource["id"],
            'email' => $this->resource["email"],
            'created_at' => $this->resource["created_at"],
            'updated_at' => $this->resource["updated_at"],
            'email_verified_at' => $this->resource["email_verified_at"],
            'last_login_at' => $this->resource["last_login_at"],
        ];
    }
}

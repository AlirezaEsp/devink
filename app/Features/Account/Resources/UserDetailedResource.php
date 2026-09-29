<?php

namespace App\Features\Account\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Features\Account\Models\User;

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
            'id' => $this->id,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'last_login_at' => $this->last_login_at,
            'username' => $this->username,
            'full_name' => $this->full_name,
            'bio' => $this->bio,
            'avatar' => $this->avatar,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}

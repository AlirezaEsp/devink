<?php

namespace App\Features\Account\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Features\Account\Models\User;

/**
 * ProfileResource
 * 
 * Profile model seriailization
 * 
 * @mixin User
 */
class PublicProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'username' => $this->username,
            'full_name' => $this->full_name,
            'bio' => $this->bio,
            'avatar' => $this->avatar,
        ];
    }
}

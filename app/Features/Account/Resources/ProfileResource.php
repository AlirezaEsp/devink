<?php

namespace App\Features\Account\Resources;

use App\Features\Account\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * ProfileResource
 * 
 * Profile model seriailization
 * 
 * @mixin Profile
 */
class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'profile' => [
                'id' => $this->id,
                'user_id' => $this->user_id,
                'full_name' => $this->full_name,
                'bio' => $this->bio,
                'avatar' => $this->avatar
            ]
        ];
    }
}

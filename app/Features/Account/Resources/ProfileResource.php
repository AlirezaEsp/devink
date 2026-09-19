<?php

namespace App\Features\Account\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Features\Account\Models\Profile;

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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'full_name' => $this->full_name,
            'bio' => $this->bio,
            'avatar' => $this->avatar,
            'updated_at' => $this->updated_at
        ];
    }
}

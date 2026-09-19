<?php

namespace App\Features\Account\Responses;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Features\Account\Resources\ProfileResource;

/**
 * UpdateProfileResponse
 * 
 * @mixin ProfileResource
 */
class UpdateProfileResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => 'Profile updated successfully.',
            'profile' => new ProfileResource($this->resource),
        ];
    }
}

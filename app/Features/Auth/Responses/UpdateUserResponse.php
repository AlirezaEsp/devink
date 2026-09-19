<?php

namespace App\Features\Auth\Responses;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Features\Auth\Resources\UserDetailedResource;

/**
 * UpdateUserResponse
 * 
 * @mixin UserDetailedResource
 */
class UpdateUserResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'mesage' => 'User informations updated successfully.',
            'user' => new UserDetailedResource($this->resource)
        ];
    }
}

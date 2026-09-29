<?php

namespace App\Features\Account\Responses;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Features\Account\Resources\UserDetailedResource;

/**
 * ResetPasswordResponse
 * 
 * @mixin UserDetailedResource
 */
class ResetPasswordResponse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => __($this->resource['status']),
            'user' => new UserDetailedResource($this->resource['user'])
        ];
    }
}

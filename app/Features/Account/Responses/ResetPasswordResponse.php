<?php

namespace App\Features\Account\Responses;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Features\Account\Resources\UserResource;

/**
 * ResetPasswordResponse
 * 
 * @mixin UserResource
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
            'user' => new UserResource($this->resource['user'])
        ];
    }
}

<?php

namespace App\Features\Account\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Features\Account\Models\User;
use App\Features\Account\Resources\ProfileResource;
use App\Features\Account\Resources\PublicProfileResource;
use App\Features\Account\Requests\UpdateProfileRequest;

class ProfileController
{    
    /**
     * PublicShow
     *
     * @param string $username username route parameter from url
     *
     * @return PublicProfileResource
     */
    public function showPublic(string $username): PublicProfileResource
    {
        // find user
        $user = User::where('username', $username)->firstOrFail();

        // return public profile resource
        return new PublicProfileResource($user);
    }

    /**
     * Show
     *
     * @param Request $request
     *
     * @return ProfileResource
     */
    public function show(Request $request): ProfileResource
    {
        // find user
        $user = $request->user();

        // return profile resource
        return new ProfileResource($user);
    }
    
    /**
     * Update
     *
     * @param UpdateProfileRequest $request
     *
     * @return JsonResponse
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        // get user
        $user = $request->user();

        // get user update (validated) data from request
        $updateData = $request->validated();

        // update profile
        $user->update($updateData);

        // refresh user instance
        $user->refresh();

        // return successful message along the profile info
        return response()->json([
            'message' => 'Profile updated successfully.',
            'profile' => new ProfileResource($user),
        ]);
    }
}

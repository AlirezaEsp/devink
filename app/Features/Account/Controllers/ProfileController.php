<?php

namespace App\Features\Account\Controllers;

use Illuminate\Http\Request;
use App\Features\Account\Resources\ProfileResource;
use App\Features\Account\Resources\PublicProfileResource;
use App\Features\Account\Requests\UpdateProfileRequest;
use App\Features\Account\Services\UpdateProfileService;
use App\Features\Account\Responses\UpdateProfileResponse;
use App\Features\Account\Models\User;

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
     * @param Request $request Request coming from client
     *
     * @return void
     */
    public function show(Request $request): ProfileResource
    {
        // find user's profile
        $user = $request->user();

        return new ProfileResource($user);
    }
    
    /**
     * Update
     *
     * @param UpdateProfileRequest $request Request coming from client
     * @param UpdateProfileService $service Related Service
     *
     * @return UpdateProfileResponse
     */
    public function update(UpdateProfileRequest $request, UpdateProfileService $service): UpdateProfileResponse
    {
        $updatedUser = $service->updateProfile(
            $request->user(),
            $request->validated(),
        );

        return new UpdateProfileResponse($updatedUser);
    }
}

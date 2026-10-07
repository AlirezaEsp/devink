<?php

namespace App\Features\Account\Controllers;

use Illuminate\Http\Request;
use App\Features\Account\Resources\ProfileResource;
use App\Features\Account\Resources\PublicProfileResource;
use App\Features\Account\Requests\UpdateProfileRequest;
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
     * @param Request $request
     *
     * @return void
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
     * @return UpdateProfileResponse
     */
    public function update(UpdateProfileRequest $request): UpdateProfileResponse
    {
        // get user
        $user = $request->user();

        // get user update (validated) data from request
        $updateData = $request->validated();

        // update profile
        $user->update($updateData);

        // refresh user instance
        $user->refresh();

        // return successful response
        return new UpdateProfileResponse($user);
    }
}

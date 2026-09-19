<?php

namespace App\Features\Account\Controllers;

use Illuminate\Http\Request;
use App\Features\Account\Resources\ProfileResource;
use App\Features\Account\Requests\UpdateProfileRequest;
use App\Features\Account\Services\UpdateProfileService;
use App\Features\Account\Responses\UpdateProfileResponse;

class ProfileController
{    
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
        $profile = $request->user()->profile;

        return new ProfileResource($profile);
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
        $profile = $service->updateProfile(
            $request->user(),
            $request->validated(),
        );

        return new UpdateProfileResponse($profile);
    }
}

<?php

namespace App\Features\Account\Controllers;

use Illuminate\Http\Request;
use App\Features\Account\Resources\ProfileResource;

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
}

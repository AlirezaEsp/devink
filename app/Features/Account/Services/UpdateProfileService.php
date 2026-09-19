<?php

namespace App\Features\Account\Services;

use App\Features\Account\Models\Profile;
use App\Features\Auth\Models\User;

/**
 * UpdateProfileService
 * 
 * Performs profile updating tasks
 */
class UpdateProfileService
{    
    /**
     * Method update
     *
     * @param User $user User instance coming from request
     * @param array $data Profile new data to change
     *
     * @return Profile
     */
    public function updateProfile(User $user, array $data): Profile
    {
        $profile = $user->profile;
        $profile->update($data);

        return $profile->refresh();
    }
}

<?php

namespace App\Features\Account\Services;

use App\Features\Account\Models\User;


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
     * @return User
     */
    public function updateProfile(User $user, array $data): User
    {
        // lowercase username
        if (isset($data['username'])) {
            $data['username'] = strtolower($data['username']);
        }

        // update profile
        $user->update($data);

        return $user->refresh();
    }
}

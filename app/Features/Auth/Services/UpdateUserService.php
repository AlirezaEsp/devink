<?php

namespace App\Features\Auth\Services;

use App\Features\Auth\Models\User;

/**
 * UpdateUserService
 * 
 * Performs user updating tasks
 */
class UpdateUserService
{    
    /**
     * Method updateUser
     *
     * @param User $user User who maked request
     * @param array $data New user data to replace
     *
     * @return User
     */
    public function updateUser(User $user, array $data): User
    {
        // lower email
        if (isset($data['email'])) {
            $data['email'] = strtolower($data['email']);
        }

        // update user data
        $user->update($data);

        return $user->refresh();
    }
}
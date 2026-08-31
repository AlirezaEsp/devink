<?php

namespace App\Features\Auth\Services;

use App\Features\Auth\Models\User;

/**
 * LogoutService
 * 
 * Performs user logging out tasks
 */
class LogoutService
{    
    /**
     * Method logoutUser
     *
     * @param User $user Authenticated user object from LogoutRequest
     *
     * @return User The same logged out user
     */
    public function logoutUser(User $user): User {
        // delete current valid access token
        $user->currentAccessToken()->delete();

        return $user;
    }
}
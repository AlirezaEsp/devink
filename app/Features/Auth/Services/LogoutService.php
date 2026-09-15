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
     * @param User $user Authenticated user object from Request
     *
     * @return array Array containing the user
     */
    public function logoutUser(User $user): array {
        // delete current valid access token
        $user->currentAccessToken()->delete();

        return [
            'user' => $user
        ];
    }
}
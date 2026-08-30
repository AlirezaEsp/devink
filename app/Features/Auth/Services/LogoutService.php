<?php

namespace App\Features\Auth\Services;

use App\Features\Auth\Models\User;

class LogoutService
{
    public function logoutUser(User $user): User {
        $user->currentAccessToken()->delete();

        return $user;
    }
}
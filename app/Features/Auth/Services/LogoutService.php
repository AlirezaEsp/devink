<?php

namespace App\Features\Auth\Services;

use App\Features\Auth\Models\User;

class LogoutService
{
    public function logoutUser(User $user): void {
        $user->currentAccessToken()->delete();
    }
}
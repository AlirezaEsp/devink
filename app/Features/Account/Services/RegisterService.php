<?php

namespace App\Features\Account\Services;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use App\Features\Account\Models\User;

/**
 * RegisterService
 * 
 * Performs user registering tasks
 */
class RegisterService
{    
    /**
     * Method registerUser
     *
     * @param array $data Received credentials from user
     *
     * @return User New User instance
     */
    public function registerUser(array $data): User {
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        event(new Registered($user));

        return $user;
    }
}
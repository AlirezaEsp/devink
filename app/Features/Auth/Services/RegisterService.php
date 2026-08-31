<?php

namespace App\Features\Auth\Services;

use App\Features\Auth\Models\User;
use Illuminate\Support\Facades\Hash;

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
     * @param array $data Valiadated user fillable data came from LoginRequest
     *
     * @return User Registered user
     */
    public function registerUser(array $data): User {
        // add user to db
        return User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }
}

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
     * @return array array containing the user
     */
    public function registerUser(array $data): array {
        // add user to db
        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
        
        return [
            'user' => $user
        ];
    }
}

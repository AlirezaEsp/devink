<?php

namespace App\Features\Auth\Services;

use App\Features\Auth\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

/**
 * LoginService
 * 
 * Performs user logging in tasks
 */
class LoginService
{    
    /**
     * Method loginUser
     *
     * @param array $credentials Received credentials from user
     *
     * @return array Array containing the user and the generated token
     */
    public function loginUser(array $credentials): array {
        // find user
        $user = User::where('email', $credentials['email'])->first();

        // check for password
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new AuthenticationException(
                'The provided credentials is not valid.'
            );
        }

        // generate token
        $token = $user->createToken('api')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}
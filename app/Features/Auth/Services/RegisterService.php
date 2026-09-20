<?php

namespace App\Features\Auth\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Features\Auth\Models\User;
use App\Features\Auth\Events\UserRegistered;

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
     * @return User Registered user instance
     */
    public function registerUser(array $data): User {
        return DB::transaction(function () use ($data) {
            // add user to db
            $user = User::create([
                'email' => strtolower($data['email']),
                'password' => Hash::make($data['password'])
            ]);

            // dispatch event for CreateProfileOnUserRegistered listener
            UserRegistered::dispatch(
                $user,
                [
                    'username' => strtolower($data['username']),
                    'full_name' => $data['full_name'],
                    'bio' => $data['bio'] ?? null,
                    'avatar' => $data['avatar'] ?? null,
                ]
            );

            // return user + profile
            return $user;
        });
    }
}

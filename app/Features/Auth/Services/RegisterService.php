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
     * @return array array containing the user
     */
    public function registerUser(array $data): array {
        return DB::transaction(function () use ($data) {
            // add user to db
            $user = User::create([
                'email' => $data['email'],
                'password' => Hash::make($data['password'])
            ]);

            // dispatch event for CreateProfileOnUserRegistered listener
            UserRegistered::dispatch(
                $user,
                [
                    'username' => $data['username'],
                    'full_name' => $data['full_name'],
                    'bio' => $data['bio'] ?? null,
                    'avatar' => $data['avatar'] ?? null,
                ]
            );

            // return user + profile
            return [
                'user' => $user->load('profile')
            ];
        });
    }
}

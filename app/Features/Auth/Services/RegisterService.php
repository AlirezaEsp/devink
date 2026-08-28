<?php

namespace App\Features\Auth\Services;

use App\Features\Auth\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterService
{
    public function execute(array $data): User {
        return User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }
}

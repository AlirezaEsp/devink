<?php

namespace App\Features\Auth\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;


/**
 * ResetPasswordService
 * 
 * Performs password reset functionality
 */
class ResetPasswordService
{
    public function resetPassword(array $credentials): array
    {
        // null user to assign in future
        $user = null;

        // status var from reset method of password facade
        $status = Password::reset(
            $credentials,
            // closure
            function ($resetUser, string $password) use (&$user): void {
                // replace users password
                $resetUser->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                // Invalidate tokens created before the password reset.
                $resetUser->tokens()->delete();

                // updated user instance
                $user = $resetUser;
            }
        );

        return [
            'status' => $status,
            'user' => $user
        ];
    }
}
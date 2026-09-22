<?php

namespace App\Features\Auth\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;


/**
 * ForgotPasswordService
 * 
 * Performs password forgot functionality
 */
class ForgotPasswordService
{
    public function forgotPassword(array $userEmail): string
    {
        $status = Password::sendResetLink($userEmail);

        if ($status !== Password::RESET_LINK_SENT) {
            $status = "The request is not valid.";
        }

        return $status;
    }
}
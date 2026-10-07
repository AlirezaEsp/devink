<?php

namespace App\Features\Account\Services;

use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Features\Account\Models\User;

/**
 * EmailVerificationService
 */
class EmailVerificationService
{    
    /**
     * Method resendVerificationEmail
     *
     * @param User $user authenticated user instance
     *
     * @return void
     */
    public function resendVerificationEmail(User $user): void {
        if ($user->hasVerifiedEmail()) {
            // here needs a refactor to have an specialized exception
            throw new HttpException(
                409,
                'The email has already been verified.'
            );
        }

        $user->sendEmailVerificationNotification();
    }
}
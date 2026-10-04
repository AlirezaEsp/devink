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
     * Method verifyEmail
     *
     * @param int $id user id from url
     * @param string $hash hash from url
     *
     * @return void
     */
    public function verifyEmail(int $id, string $hash): void {
        // find user
        $user = User::findOrFail($id);
        

        if ($user->hasVerifiedEmail()) {
            return;
        }

        // check user email with hash version
        $hashEquals = hash_equals(sha1($user->getEmailForVerification()), $hash);

        if (!$hashEquals) {
            // here needs a refactor to have an specialized exception
            throw new HttpException(
                403,
                'The verification link is invalid.'
            );
        }

        $user->markEmailAsVerified();
    }
    
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
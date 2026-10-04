<?php

namespace App\Features\Account\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use App\Features\Account\Models\User;

class AccountServiceProvider extends ServiceProvider
{
    /**
     * Register any account feature services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any account feature services.
     */
    public function boot(): void
    {
        VerifyEmail::createUrlUsing(function (User $user) {
            $verificationUrl = URL::temporarySignedRoute(
                'accounts.auth.verification.verify',
                now()->addMinutes(60),
                [
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                ]
            );

            return config('app.frontend_url')
            . '/email/verify?url='
            . urlencode($verificationUrl);
        });
    }
}

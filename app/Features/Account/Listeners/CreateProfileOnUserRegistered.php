<?php

namespace App\Features\Account\Listeners;

use App\Features\Auth\Events\UserRegistered;

class CreateProfileOnUserRegistered
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        $event->user->profile()->create($event->profileData);
    }
}

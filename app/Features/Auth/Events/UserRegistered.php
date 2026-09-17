<?php

namespace App\Features\Auth\Events;

use Illuminate\Foundation\Events\Dispatchable;

use App\Features\Auth\Models\User;

class UserRegistered
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(public User $user, public array $profileData)
    {
        //
    }

}

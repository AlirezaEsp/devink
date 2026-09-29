<?php

namespace App\Features\Account\Events;

use Illuminate\Foundation\Events\Dispatchable;
use App\Features\Account\Models\User;

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

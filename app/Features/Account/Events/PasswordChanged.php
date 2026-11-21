<?php

namespace App\Features\Account\Events;

use App\Features\Account\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Fired when a user's password is changed, either from an authenticated
 * session or through the password reset flow.
 *
 * Kept in the Account feature because that is where the trigger lives.
 */
final readonly class PasswordChanged
{
    use Dispatchable;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param string|null $ip
     * @param string|null $userAgent
     * @param string $changedAt
     */
    public function __construct(
        public User $user,
        public ?string $ip = null,
        public ?string $userAgent = null,
        public string $changedAt = '',
    ) {}
}

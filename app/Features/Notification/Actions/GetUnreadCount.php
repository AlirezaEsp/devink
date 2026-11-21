<?php

namespace App\Features\Notification\Actions;

use App\Features\Account\Models\User;

class GetUnreadCount
{
    public function handle(User $user): int
    {
        return $user->unreadNotifications()->count();
    }
}

<?php

namespace App\Features\Notification\Actions;

use App\Features\Account\Models\User;

class MarkAllNotificationsAsRead
{
    public function handle(User $user): int
    {
        return $user->unreadNotifications()->update(['read_at' => now()]);
    }
}

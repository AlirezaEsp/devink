<?php

namespace App\Features\Notification\Actions;

use App\Features\Notification\Models\Notification;
use App\Features\Account\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class MarkNotificationAsRead
{
    public function handle(User $user, string $id): Notification
    {
        if (! Str::isUuid($id)) {
            throw (new ModelNotFoundException)->setModel(Notification::class, [$id]);
        }

        $notification = $user->notifications()->whereKey($id)->firstOrFail();

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return $notification->refresh();
    }
}

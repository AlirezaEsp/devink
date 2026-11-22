<?php

namespace App\Features\Notification\Actions;

use App\Features\Notification\Models\Notification;
use App\Features\Account\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class DeleteNotification
{
    public function handle(User $user, string $id): void
    {
        if (! Str::isUuid($id)) {
            throw (new ModelNotFoundException)->setModel(Notification::class, [$id]);
        }

        $user->notifications()->whereKey($id)->firstOrFail()->delete();
    }
}

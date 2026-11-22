<?php

namespace App\Features\Notification\Actions;

use App\Features\Notification\DTOs\NotificationFilterDTO;
use App\Features\Notification\Enums\NotificationType;
use App\Features\Account\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListNotifications
{
    public function handle(User $user, NotificationFilterDTO $filter): LengthAwarePaginator
    {
        $types = array_map(
            fn(NotificationType $type) => $type->value,
            $filter->types()
        );

        return $user->notifications()
            ->when(
                $filter->onlyUnread === true,
                fn($query) => $query->whereNull('read_at'),
            )
            ->when(
                $filter->onlyUnread === false,
                fn($query) => $query->whereNotNull('read_at'),
            )
            ->when(
                $types !== [],
                fn($query) => $query->whereIn('data->type', $types),
            )
            ->latest()
            ->paginate($filter->perPage);
    }
}

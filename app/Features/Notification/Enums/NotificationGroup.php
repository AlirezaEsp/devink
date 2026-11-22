<?php

namespace App\Features\Notification\Enums;

enum NotificationGroup: string
{
    case Content   = 'content';
    case Social    = 'social';
    case Editorial = 'editorial';
    case Account   = 'account';

    public function label(): string
    {
        return match ($this) {
            self::Content   => 'مقالات من',
            self::Social    => 'تعامل‌ها',
            self::Editorial => 'بازبینی و مدیریت',
            self::Account   => 'حساب کاربری',
        };
    }

    public function types(): array
    {
        return array_values(array_filter(
            NotificationType::cases(),
            fn(NotificationType $type) => $type->group() === $this
        ));
    }
}

<?php

namespace App\Features\Account\Listeners;

use App\Features\Account\Events\PasswordChanged;
use App\Features\Notification\Enums\NotificationType;
use App\Features\Notification\Notifications\AppNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Sends a security alert to the user after their password changes.
 *
 * This is a mandatory notification: the user cannot opt out of it,
 * because it is the only signal that an account takeover happened.
 */
final class SendPasswordChangedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param PasswordChanged $event
     *
     * @return void
     */
    public function handle(PasswordChanged $event): void
    {
        $event->user->notify(new AppNotification(
            NotificationType::PasswordChanged,
            'رمز عبورت تغییر کرد',
            'رمز عبور حساب تو همین حالا تغییر کرد. اگر این کار را خودت انجام ندادی، فوراً از طریق ایمیل رمزت را بازنشانی کن.',
            [
                'changed_at' => $event->changedAt,
                'ip' => $event->ip,
                'user_agent' => $event->userAgent,
            ],
        ));
    }
}

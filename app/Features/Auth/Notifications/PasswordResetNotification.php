<?php

namespace App\Features\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly string $token)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $frontendUrl = trim(
            config('app.frontend_url', env('FRONTEND_URL'))
        );

        // constitue reset url
        $resetUrl = $frontendUrl . 'reset-password' . http_build_query([
            'token' => $this->token,
            'email' => $notifiable->email
        ]);
    
        return (new MailMessage)
            ->subject('Reset your Devink password')
            ->line('You requested a password reset.')
            ->action('Reset Password', $resetUrl)
            ->line('This password reset link will expire soon.')
            ->line('If you did not request this, no action is required.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

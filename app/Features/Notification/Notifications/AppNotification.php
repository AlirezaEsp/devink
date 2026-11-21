<?php

namespace App\Features\Notification\Notifications;

use App\Features\Notification\Enums\NotificationType;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param NotificationType $type notification type
     * @param string $title short title shown in the bell
     * @param string $body human readable message
     * @param array $payload extra data for the client (url, ids, actor, ...)
     */
    public function __construct(
        private readonly NotificationType $type,
        private readonly string $title,
        private readonly string $body,
        private readonly array $payload = [],
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // use the default channels of this notification type
        return $this->type->defaultChannels();
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // this is what lands in the `data` column
        return [
            'type' => $this->type->value,
            'title' => $this->title,
            'body' => $this->body,
            'payload' => $this->payload,
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->title)
            ->line($this->body);

        // add an action button when a url exists
        if (! empty($this->payload['url'])) {
            $message->action('مشاهده', url($this->payload['url']));
        }

        return $message;
    }

    /**
     * Get the notification type.
     */
    public function getType(): NotificationType
    {
        return $this->type;
    }
}


/* how to use?
//single receiver
use App\Features\Notification\Enums\NotificationType;
use App\Features\Notification\Notifications\AppNotification;

$article->user->notify(new AppNotification(
    NotificationType::NewComment,
    'نظر جدید',
    "{$comment->user->name} روی مقالهٔ «{$article->title}» نظر داد",
    [
        'actor' => [
            'id' => $comment->user->id,
            'name' => $comment->user->name,
            'avatar' => $comment->user->avatar_url,
        ],
        'article_id' => $article->id,
        'comment_id' => $comment->id,
        'url' => "/@{$article->user->username}/{$article->slug}#comment-{$comment->id}",
    ],
));

//multi receiver

use Illuminate\Support\Facades\Notification;

Notification::send(
    User::where('role', 'editor')->get(),
    new AppNotification(
        NotificationType::ReviewRequested,
        'درخواست بازبینی',
        "مقالهٔ «{$article->title}» برای بازبینی ارسال شد",
        ['article_id' => $article->id, 'url' => "/admin/reviews/{$article->id}"],
    ),
);

*/
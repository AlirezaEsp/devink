<?php

namespace Database\Seeders;

use App\Features\Account\Models\User;
use App\Features\Notification\Enums\NotificationType;
use App\Features\Notification\Notifications\AppNotification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Seed the application's notifications.
     */
    public function run(): void
    {
        // main receiver of the seeded notifications
        $user = User::where('email', 'user@example.com')->firstOrFail();

        // someone else, used as the actor inside payloads
        $actor = User::where('id', '!=', $user->id)->firstOrFail();

        $actorData = [
            'id' => $actor->id,
            'name' => $actor->full_name,
            'avatar' => $actor->avatar,
        ];

        // a few notifications of different types and groups
        $user->notify(new AppNotification(
            NotificationType::NewFollower,
            'دنبال‌کننده جدید',
            "{$actor->full_name} شما را دنبال کرد",
            [
                'actor' => $actorData,
                'url' => "/@{$actor->username}",
            ],
        ));

        $user->notify(new AppNotification(
            NotificationType::NewComment,
            'نظر جدید',
            "{$actor->full_name} روی مقالهٔ شما نظر داد",
            [
                'actor' => $actorData,
                'article_id' => 1,
                'comment_id' => 1,
                'url' => '/@string/article-1#comment-1',
            ],
        ));

        $user->notify(new AppNotification(
            NotificationType::CommentReply,
            'پاسخ به نظر',
            "{$actor->full_name} به نظر شما پاسخ داد",
            [
                'actor' => $actorData,
                'article_id' => 1,
                'comment_id' => 2,
                'url' => '/@string/article-1#comment-2',
            ],
        ));

        $user->notify(new AppNotification(
            NotificationType::ArticleLiked,
            'واکنش جدید',
            "{$actor->full_name} به مقالهٔ شما واکنش داد",
            [
                'actor' => $actorData,
                'article_id' => 1,
                'count' => 1,
                'url' => '/@string/article-1',
            ],
        ));

        $user->notify(new AppNotification(
            NotificationType::ArticlePublished,
            'مقاله منتشر شد',
            'مقالهٔ «مقدمه‌ای بر صف‌ها در لاراول» منتشر شد',
            [
                'article_id' => 1,
                'slug' => 'laravel-queues',
                'url' => '/@string/laravel-queues',
            ],
        ));

        $user->notify(new AppNotification(
            NotificationType::ArticleAccepted,
            'مقاله تأیید شد',
            'مقالهٔ «مقدمه‌ای بر صف‌ها در لاراول» تأیید شد',
            [
                'article_id' => 1,
                'slug' => 'laravel-queues',
                'url' => '/@string/laravel-queues',
            ],
        ));

        $user->notify(new AppNotification(
            NotificationType::NewArticleFromFollowing,
            'مقالهٔ جدید از دنبال‌شده‌ها',
            "{$actor->full_name} مقالهٔ تازه‌ای منتشر کرد",
            [
                'actor' => $actorData,
                'article_id' => 2,
                'slug' => 'clean-code',
                'url' => "/@{$actor->username}/clean-code",
            ],
        ));

        $user->notify(new AppNotification(
            NotificationType::CommentMention,
            'منشن شدید',
            "{$actor->full_name} در یک نظر شما را منشن کرد",
            [
                'actor' => $actorData,
                'article_id' => 1,
                'comment_id' => 3,
                'url' => '/@string/article-1#comment-3',
            ],
        ));

        // notifications for another user, to test isolation between accounts
        $actor->notify(new AppNotification(
            NotificationType::NewFollower,
            'دنبال‌کننده جدید',
            "{$user->full_name} شما را دنبال کرد",
            [
                'actor' => [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'avatar' => $user->avatar,
                ],
                'url' => "/@{$user->username}",
            ],
        ));

        $actor->notify(new AppNotification(
            NotificationType::NewComment,
            'نظر جدید',
            "{$user->full_name} روی مقالهٔ شما نظر داد",
            [
                'actor' => [
                    'id' => $user->id,
                    'name' => $user->full_name,
                    'avatar' => $user->avatar,
                ],
                'article_id' => 3,
                'comment_id' => 4,
                'url' => "/@{$actor->username}/article-3#comment-4",
            ],
        ));

        // leave some unread, mark the oldest ones as read so filters are testable
        $user->notifications()
            ->orderBy('created_at')
            ->limit(3)
            ->update(['read_at' => now()]);
    }
}

<?php

namespace App\Features\Reaction\Listeners;

use App\Features\Notification\Enums\NotificationType;
use App\Features\Notification\Notifications\AppNotification;
use App\Features\Reaction\Events\ArticleLiked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Turns an article like into a single aggregated notification.
 *
 * One row per (article, type) instead of one row per like, so a post with
 * 500 likes still produces a single unread badge.
 */
final class SendArticleLikedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * How many actor names to keep in the payload.
     *
     * @var int
     */
    private const ACTOR_LIMIT = 3;

    /**
     * Handle the event.
     *
     * @param ArticleLiked $event
     *
     * @return void
     */
    public function handle(ArticleLiked $event): void
    {
        $reaction = $event->reaction;
        $article = $reaction->article;
        $actor = $reaction->user;

        // never notify someone about their own like
        if ($article->user_id === $actor->id) {
            return;
        }

        $owner = $article->user;
        $type = NotificationType::ArticleLiked;

        // the real number of likes, so unlike/like cycles cannot inflate it
        $count = $article->reactions()->count();

        $actors = $article->reactions()
            ->with('user:id,name,avatar')
            ->latest()
            ->take(self::ACTOR_LIMIT)
            ->get()
            ->pluck('user')
            ->filter()
            ->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar,
            ])
            ->values()
            ->all();

        $payload = [
            'article_id' => $article->id,
            'slug' => $article->slug,
            'count' => $count,
            'actors' => $actors,
            'url' => "/@{$owner->username}/{$article->slug}",
        ];

        $existing = $owner->notifications()
            ->where('data->type', $type->value)
            ->whereRaw("(data #>> '{payload,article_id}')::bigint = ?", [$article->id])
            ->first();

        // a fresh like after the old one was read should resurface the badge
        if ($existing) {
            $existing->update([
                'data' => [
                    'type' => $type->value,
                    'title' => $this->title($count, $actors),
                    'body' => $this->body($count, $actors),
                    'payload' => $payload,
                ],
                'read_at' => null,
            ]);

            return;
        }

        $owner->notify(new AppNotification(
            $type,
            $this->title($count, $actors),
            $this->body($count, $actors),
            $payload,
        ));
    }

    /**
     * Build the notification title.
     *
     * @param int $count
     * @param array $actors
     *
     * @return string
     */
    private function title(int $count, array $actors): string
    {
        if ($count === 1) {
            return 'مقاله‌ات پسندیده شد';
        }

        return "مقاله‌ات را {$count} نفر پسندیدند";
    }

    /**
     * Build the notification body.
     *
     * @param int $count
     * @param array $actors
     *
     * @return string
     */
    private function body(int $count, array $actors): string
    {
        $names = array_column($actors, 'name');

        if ($count === 1) {
            return "{$names[0]} مقاله‌ات را پسندید.";
        }

        $others = $count - count($names);

        if ($others > 0) {
            return "{$names[0]} و {$others} نفر دیگر مقاله‌ات را پسندیدند.";
        }

        return implode('، ', $names) . ' مقاله‌ات را پسندیدند.';
    }
}

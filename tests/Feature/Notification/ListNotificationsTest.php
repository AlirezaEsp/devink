<?php

use App\Features\Notification\Enums\NotificationType;
use App\Features\Notification\Notifications\AppNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Features\Account\Models\User;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

function listNotificationsUrl(string $query = ''): string
{
    return route('notifications.index') . $query;
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->token = $this->user->createToken('api')->plainTextToken;
});

describe('Index', function () {

    describe('Success', function () {

        it('lists only the notifications of the authenticated user.', function () {
            $other = makeOtherUser();

            $this->user->notify(new AppNotification(NotificationType::NewFollower, 'مال من', 'متن'));
            $this->user->notify(new AppNotification(NotificationType::NewComment, 'مال من ۲', 'متن'));
            $other->notify(new AppNotification(NotificationType::NewFollower, 'مال دیگری', 'متن'));

            $response = getJson(
                listNotificationsUrl(),
                generateAuthHeader($this->token)
            );

            $response->assertOk()->assertJsonCount(2, 'data');

            $titles = collect($response->json('data'))->pluck('title')->all();

            expect($titles)->toContain('مال من');
            expect($titles)->toContain('مال من ۲');
            expect($titles)->not->toContain('مال دیگری');
        });

        it('returns an empty list when the user has no notifications.', function () {
            $response = getJson(
                listNotificationsUrl(),
                generateAuthHeader($this->token)
            );

            $response
                ->assertOk()
                ->assertJsonCount(0, 'data');
        });

        it('returns the expected resource structure.', function () {
            $this->user->notify(new AppNotification(NotificationType::NewFollower, 'تست', 'متن'));

            $response = getJson(
                listNotificationsUrl(),
                generateAuthHeader($this->token)
            );

            $response
                ->assertOk()
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'type',
                            'group',
                            'title',
                            'body',
                            'actor',
                            'url',
                            'payload',
                            'is_read',
                            'read_at',
                            'created_at',
                        ],
                    ],
                    'links',
                    'meta',
                ]);
        });

        it('filters unread notifications only.', function () {
            $this->user->notify(new AppNotification(NotificationType::NewFollower, 'خوانده‌شده', 'متن'));

            $this->user->notifications()->update(['read_at' => now()]);

            $this->user->notify(new AppNotification(NotificationType::NewFollower, 'نخوانده', 'متن'));

            $response = getJson(
                listNotificationsUrl('?only_unread=1'),
                generateAuthHeader($this->token)
            );

            $response
                ->assertOk()
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.title', 'نخوانده')
                ->assertJsonPath('data.0.is_read', false);
        });

        it('filters read notifications only.', function () {
            $this->user->notify(new AppNotification(NotificationType::NewFollower, 'خوانده‌شده', 'متن'));

            $this->user->notifications()->update(['read_at' => now()]);

            $this->user->notify(new AppNotification(NotificationType::NewFollower, 'نخوانده', 'متن'));

            $response = getJson(
                listNotificationsUrl('?only_unread=0'),
                generateAuthHeader($this->token)
            );

            $response
                ->assertOk()
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.title', 'خوانده‌شده')
                ->assertJsonPath('data.0.is_read', true);
        });

        it('ignores an invalid only_unread value.', function () {
            $this->user->notify(new AppNotification(NotificationType::NewFollower, 'تست', 'متن'));

            $response = getJson(
                listNotificationsUrl('?only_unread=maybe'),
                generateAuthHeader($this->token)
            );

            $response
                ->assertOk()
                ->assertJsonCount(1, 'data');
        });

        it('filters by notification type.', function () {
            $this->user->notify(new AppNotification(NotificationType::NewFollower, 'فالوو', 'متن'));
            $this->user->notify(new AppNotification(NotificationType::NewComment, 'نظر ۱', 'متن'));
            $this->user->notify(new AppNotification(NotificationType::NewComment, 'نظر ۲', 'متن'));

            $response = getJson(
                listNotificationsUrl('?type=new_comment'),
                generateAuthHeader($this->token)
            );

            $response
                ->assertOk()
                ->assertJsonCount(2, 'data')
                ->assertJsonPath('data.0.type', 'new_comment');
        });

        it('filters by notification group.', function () {
            $this->user->notify(new AppNotification(NotificationType::NewFollower, 'فالوو', 'متن'));
            $this->user->notify(new AppNotification(NotificationType::ArticleAccepted, 'تأیید', 'متن'));

            $response = getJson(
                listNotificationsUrl('?group=social'),
                generateAuthHeader($this->token)
            );

            $response
                ->assertOk()
                ->assertJsonCount(1, 'data')
                ->assertJsonPath('data.0.group', 'social');
        });

        it('clamps per_page to the maximum allowed value.', function () {
            $this->user->notify(new AppNotification(NotificationType::NewFollower, 'تست', 'متن'));

            $response = getJson(
                listNotificationsUrl('?per_page=5000'),
                generateAuthHeader($this->token)
            );

            $response
                ->assertOk()
                ->assertJsonPath('meta.per_page', 100);
        });

        it('clamps per_page to the minimum allowed value.', function () {
            $this->user->notify(new AppNotification(NotificationType::NewFollower, 'تست', 'متن'));

            $response = getJson(
                listNotificationsUrl('?per_page=0'),
                generateAuthHeader($this->token)
            );

            $response
                ->assertOk()
                ->assertJsonPath('meta.per_page', 1);
        });
    });

    describe('Authentication Failure', function () {

        it('rejects an invalid token.', function () {
            $response = getJson(
                listNotificationsUrl(),
                generateAuthHeader($this->token . 'X')
            );

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.',
                ]);
        });

        it('rejects a request without an access token.', function () {
            $response = getJson(listNotificationsUrl());

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.',
                ]);
        });
    });
});

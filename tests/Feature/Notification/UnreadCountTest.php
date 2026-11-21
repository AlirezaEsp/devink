<?php

use App\Features\Notification\Enums\NotificationType;
use App\Features\Notification\Notifications\AppNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Features\Account\Models\User;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

function unreadCountUrl(): string
{
    return route('notifications.unread.count');
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->token = $this->user->createToken('api')->plainTextToken;
});

describe('UnreadCount', function () {

    describe('Success', function () {

        it('counts unread notifications of the authenticated user only.', function () {
            $other = makeOtherUser();

            $this->user->notify(new AppNotification(NotificationType::NewFollower, '۱', 'متن'));
            $this->user->notify(new AppNotification(NotificationType::NewFollower, '۲', 'متن'));
            $this->user->notify(new AppNotification(NotificationType::NewFollower, '۳', 'متن'));
            $other->notify(new AppNotification(NotificationType::NewFollower, 'دیگری', 'متن'));

            $this->user->notifications()
                ->where('data->title', '۱')
                ->sole()
                ->markAsRead();

            $response = getJson(
                unreadCountUrl(),
                generateAuthHeader($this->token)
            );

            $response
                ->assertOk()
                ->assertExactJsonStructure(['count'])
                ->assertJsonPath('count', 2);
        });

        it('returns zero when every notification is read.', function () {
            $this->user->notify(new AppNotification(NotificationType::NewFollower, 'تست', 'متن'));

            $this->user->notifications()->update(['read_at' => now()]);

            $response = getJson(
                unreadCountUrl(),
                generateAuthHeader($this->token)
            );

            $response
                ->assertOk()
                ->assertJsonPath('count', 0);
        });

        it('returns zero when the user has no notifications.', function () {
            $response = getJson(
                unreadCountUrl(),
                generateAuthHeader($this->token)
            );

            $response
                ->assertOk()
                ->assertJsonPath('count', 0);
        });
    });

    describe('Authentication Failure', function () {

        it('rejects a request without an access token.', function () {
            $response = getJson(unreadCountUrl());

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.',
                ]);
        });
    });
});

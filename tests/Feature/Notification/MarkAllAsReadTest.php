<?php

use App\Features\Notification\Enums\NotificationType;
use App\Features\Notification\Notifications\AppNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Features\Account\Models\User;

use function Pest\Laravel\patchJson;

uses(RefreshDatabase::class);

function markAllAsReadUrl(): string
{
    return route('notifications.read.all');
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->token = $this->user->createToken('api')->plainTextToken;
});

describe('MarkAllAsRead', function () {

    describe('Success', function () {

        it('marks every notification of the user as read.', function () {
            $this->user->notify(new AppNotification(NotificationType::NewFollower, '۱', 'متن'));
            $this->user->notify(new AppNotification(NotificationType::NewFollower, '۲', 'متن'));
            $this->user->notify(new AppNotification(NotificationType::NewFollower, '۳', 'متن'));

            $response = patchJson(
                markAllAsReadUrl(),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertOk();

            expect($this->user->notifications()->whereNull('read_at')->count())->toBe(0);
        });

        it('does not touch notifications of other users.', function () {
            $other = makeOtherUser();

            $this->user->notify(new AppNotification(NotificationType::NewFollower, 'مال من', 'متن'));
            $other->notify(new AppNotification(NotificationType::NewFollower, 'مال دیگری', 'متن'));

            patchJson(markAllAsReadUrl(), [], generateAuthHeader($this->token))
                ->assertOk();

            expect($this->user->notifications()->whereNull('read_at')->count())->toBe(0);
            expect($other->notifications()->whereNull('read_at')->count())->toBe(1);
        });

        it('succeeds when the user has no notifications.', function () {
            $response = patchJson(
                markAllAsReadUrl(),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertOk();
        });
    });

    describe('Authentication Failure', function () {

        it('rejects a request without an access token.', function () {
            $response = patchJson(markAllAsReadUrl(), []);

            $response->assertUnauthorized();
        });
    });
});

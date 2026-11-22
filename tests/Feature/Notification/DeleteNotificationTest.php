<?php

use App\Features\Notification\Enums\NotificationType;
use App\Features\Notification\Notifications\AppNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Features\Account\Models\User;

use function Pest\Laravel\deleteJson;

uses(RefreshDatabase::class);

function destroyNotificationUrl(string $id): string
{
    return route('notifications.destroy', ['id' => $id]);
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->token = $this->user->createToken('api')->plainTextToken;
});

describe('Destroy', function () {

    describe('Success', function () {

        it('deletes a notification of the authenticated user.', function () {
            $this->user->notify(new AppNotification(NotificationType::NewFollower, 'تست', 'متن'));

            $notification = $this->user->notifications()->sole();

            $response = deleteJson(
                destroyNotificationUrl($notification->id),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertOk();

            $this->assertDatabaseMissing('notifications', [
                'id' => $notification->id,
            ]);
        });

        it('deletes a read notification too.', function () {
            $this->user->notify(new AppNotification(NotificationType::NewFollower, 'تست', 'متن'));

            $notification = $this->user->notifications()->sole();
            $notification->markAsRead();

            $response = deleteJson(
                destroyNotificationUrl($notification->id),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertOk();

            $this->assertDatabaseMissing('notifications', [
                'id' => $notification->id,
            ]);
        });
    });

    describe('Ownership Failure', function () {

        it('cannot delete another user notification.', function () {
            $victim = makeOtherUser();

            $victim->notify(new AppNotification(NotificationType::NewFollower, 'مال دیگری', 'متن'));

            $notification = $victim->notifications()->sole();

            $response = deleteJson(
                destroyNotificationUrl($notification->id),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();

            $this->assertDatabaseHas('notifications', [
                'id' => $notification->id,
            ]);
        });
    });

    describe('Not Found', function () {

        it('returns 404 for a malformed uuid.', function () {
            $response = deleteJson(
                '/api/v1/notifications/not-a-uuid',
                [],
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();
        });

        it('returns 404 for a uuid that does not exist.', function () {
            $response = deleteJson(
                destroyNotificationUrl('00000000-0000-4000-8000-000000000000'),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();
        });
    });

    describe('Authentication Failure', function () {

        it('rejects a request without an access token.', function () {
            $this->user->notify(new AppNotification(NotificationType::NewFollower, 'تست', 'متن'));

            $notification = $this->user->notifications()->sole();

            $response = deleteJson(destroyNotificationUrl($notification->id), []);

            $response->assertUnauthorized();
        });
    });
});

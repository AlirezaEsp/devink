<?php

use App\Features\Notification\Enums\NotificationType;
use App\Features\Notification\Models\Notification;
use App\Features\Notification\Notifications\AppNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Features\Account\Models\User;

use function Pest\Laravel\patchJson;

uses(RefreshDatabase::class);

function markAsReadUrl(string $id): string
{
    return route('notifications.read', ['id' => $id]);
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->token = $this->user->createToken('api')->plainTextToken;
});

describe('MarkAsRead', function () {

    describe('Success', function () {

        it('marks a notification as read.', function () {
            $this->user->notify(new AppNotification(NotificationType::NewFollower, 'تست', 'متن'));

            $notification = $this->user->notifications()->sole();

            $response = patchJson(
                markAsReadUrl($notification->id),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertOk();

            expect(Notification::find($notification->id)->read_at)->not->toBeNull();
        });

        it('keeps the first read timestamp when called twice.', function () {
            $this->user->notify(new AppNotification(NotificationType::NewFollower, 'تست', 'متن'));

            $notification = $this->user->notifications()->sole();

            patchJson(markAsReadUrl($notification->id), [], generateAuthHeader($this->token))
                ->assertOk();

            $firstReadAt = Notification::find($notification->id)->read_at;

            $this->travel(5)->minutes();

            patchJson(markAsReadUrl($notification->id), [], generateAuthHeader($this->token))
                ->assertOk();

            expect(Notification::find($notification->id)->read_at->timestamp)
                ->toBe($firstReadAt->timestamp);
        });
    });

    describe('Ownership Failure', function () {

        it('cannot mark another user notification as read.', function () {
            $victim = makeOtherUser();

            $victim->notify(new AppNotification(NotificationType::NewFollower, 'مال دیگری', 'متن'));

            $notification = $victim->notifications()->sole();

            $response = patchJson(
                markAsReadUrl($notification->id),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();

            expect(Notification::find($notification->id)->read_at)->toBeNull();
        });
    });

    describe('Not Found', function () {

        it('returns 404 for a malformed uuid.', function () {
            $response = patchJson(
                '/api/v1/notifications/not-a-uuid/read',
                [],
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();
        });

        it('returns 404 for a uuid that does not exist.', function () {
            $response = patchJson(
                markAsReadUrl('00000000-0000-4000-8000-000000000000'),
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

            $response = patchJson(markAsReadUrl($notification->id), []);

            $response->assertUnauthorized();
        });
    });
});

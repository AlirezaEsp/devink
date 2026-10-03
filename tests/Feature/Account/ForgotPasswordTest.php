<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use function Pest\Laravel\postJson;
use App\Features\Account\Models\User;
use App\Features\Account\Notifications\PasswordResetNotification;

uses(RefreshDatabase::class);

function forgotPasswordPayload(array $overrides = []) : array {
    return array_merge([
        "email" => "user@example.com",
    ], $overrides);
}
function forgotPasswordUrl(): string {
    return route('accounts.auth.forgot-password');
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();
});

describe('ForgotPassword', function () {

    describe('Success', function () {

        it('accepts forgot-password request for a valid user email; and sends reset password email.', function () {
            // use fake notification
            Notification::fake();

            $response = postJson(forgotPasswordUrl(), forgotPasswordPayload());

            $response->
                assertOk()->
                assertJson([
                    'message' => 'We have emailed your password reset link.'
                ]);
            
            $this->
                assertDatabaseHas('password_reset_tokens', [
                    'email' => forgotPasswordPayload()['email']
                ]);
            
            // check if notification sended
            Notification::assertSentTo(
                $this->user,
                PasswordResetNotification::class,
                function (PasswordResetNotification $notification) {
                    return !empty($notification->getToken());
                }
            );
        });
    });

    describe('Validation Failure', function () {

        it('rejects an invalid email.', function () {
            $response = postJson(forgotPasswordUrl(), forgotPasswordPayload([
                'email' => 'userexamplecom'
            ]));

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(["email"]);
        });

        it('rejects an email longer than 255 chars.', function () {
            $response = postJson(forgotPasswordUrl(), forgotPasswordPayload([
                'email' => str_repeat('user', 70) . '@example.com'
            ]));

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['email']);
        });

        it('rejects forgot-password request for a not existing email.', function () {
            $response = postJson(forgotPasswordUrl(), forgotPasswordPayload([
                'email' => 'userX@example.com',
            ]));

            $response->
                assertUnprocessable()->
                assertJson([
                    'message' => 'The request is not valid.'
                ]);
        });
    });
});
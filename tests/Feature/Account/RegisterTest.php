<?php

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use function Pest\Laravel\postJson;
use App\Features\Account\Models\User;

uses(RefreshDatabase::class);

function registrationPayload(array $overrides = []) : array {
    return array_merge([
        "email" => "user@example.com",
        "password" => "stringst",
        "username" => "string",
        "full_name" => "string",
        "password_confirmation" => "stringst"
    ], $overrides);
}
function registrationUrl(): string {
    return route('accounts.auth.register');
}

describe('Register', function () {

    describe('Success', function () {

        it('registers a user successfully.', function () {
            Notification::fake();

            $response = postJson(registrationUrl(), registrationPayload());

            $response->
                assertCreated()->
                assertExactJsonStructure([
                    "message",
                    "user" => [
                        "id",
                        "email",
                        "email_verified_at",
                        "last_login_at",
                        "created_at",
                        "updated_at",
                        "deleted_at"
                    ],
                    "token"
                ])->
                assertJson([
                    'message' => 'User registered successfully. Please verify your email.'
                ]);
            
            $this->
                assertDatabaseHas('users', [
                    'email' => 'user@example.com',
                    'username' => 'string'
                ]);
            
            $this->
                assertDatabaseHas('personal_access_tokens', [
                    'tokenable_id' => $response->json('user.id')
                ]);

            Notification::assertSentTo(
                User::findOrFail($response->json('user.id')),
                VerifyEmail::class
            );
        });
    });

    describe('Validation Failure', function () {

        it('rejects an invalid email.', function () {
            $response = postJson(registrationUrl(), registrationPayload([
                'email' => 'userexamplecom'
            ]));

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(["email"]);
        });

        it('rejects an email longer than 255 chars.', function () {
            $response = postJson(registrationUrl(), registrationPayload([
                'email' => str_repeat('user', 70) . '@example.com'
            ]));

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['email']);
        });

        it('rejects passwords shorter than 8 chars.', function () {
            $response = postJson(registrationUrl(), registrationPayload([
                'password' => '1234567',
                'password_confirmation' => '1234567'
            ]));

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['password']);
        });

        it('rejects two mismatched passwords.', function () {
            $response = postJson(registrationUrl(), registrationPayload([
                'password' => 'stringst',
                'password_confirmation' => 'stringstX'
            ]));

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['password']);
        });

        it('rejects an username longer than 255 chars.', function () {
            $response = postJson(registrationUrl(), registrationPayload([
                'username' => str_repeat('string', 50)
            ]));

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['username']);
        });

        it('rejects an full name longer than 255 chars.', function () {
            $response = postJson(registrationUrl(), registrationPayload([
                'full_name' => str_repeat('string', 50)
            ]));

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['full_name']);
        });
    });

    describe('Duplication Failure', function () {

        it('prevents registration of existing email.', function () {
            $successfulResponse = postJson(registrationUrl(), registrationPayload());

            $this->
                assertDatabaseHas('users', [
                    'email' => 'user@example.com'
                ]);

            $faultResponse = postJson(registrationUrl(), registrationPayload([
                'username' => 'stringX'
            ]));

            $faultResponse->
                assertUnprocessable()->
                assertJsonValidationErrors(['email']);

        });

        it('prevents registration of existing username.', function () {
            $successfulResponse = postJson(registrationUrl(), registrationPayload());

            $this->
                assertDatabaseHas('users', [
                    'username' => 'string'
                ]);

            $faultResponse = postJson(registrationUrl(), registrationPayload([
                'email' => 'userX@example.com'
            ]));

            $faultResponse->
                assertUnprocessable()->
                assertJsonValidationErrors(['username']);

        });
    });
});
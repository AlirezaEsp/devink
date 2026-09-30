<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

function registrationPayload(array $overrides = []) : array {
    return array_merge([
        "email" => "user@example.com",
        "password" => "stringst",
        "username" => "string",
        "full_name" => "string",
        "bio" => "string",
        "avatar" => "string",
        "password_confirmation" => "stringst"
    ], $overrides);
}
function registrationUrl(): string {
    return route('accounts.auth.register');
}

describe('Registration', function () {

    describe('Success', function () {

        it('registers a user successfully.', function () {
            $response = postJson(registrationUrl(), registrationPayload());

            $response->
                assertCreated()->
                assertJsonStructure([
                    "message",
                    "user" => [
                        "id",
                        "email",
                        "created_at",
                        "updated_at",
                        "email_verified_at",
                        "last_login_at",
                    ]
                ])->
                assertJson([
                    'message' => 'User registered successfully.'
                ]);
            
            $this->
                assertDatabaseHas('users', [
                    'email' => 'user@example.com'
                ]);
            
            $this->
                assertDatabaseHas('users', [
                    'username' => 'string'
                ]);
        });
    });

    describe('Validation Failure', function () {

        it('rejects an unvalid email.', function () {
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
<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\postJson;
use App\Features\Account\Models\User;

uses(RefreshDatabase::class);

function loginPayload(array $overrides = []) : array {
    return array_merge([
        "email" => "user@example.com",
        "password" => "stringst",
    ], $overrides);
}
function loginUrl(): string {
    return route('accounts.auth.login');
}

beforeEach(function () {
    User::factory()->defaultTestingUser()->create();
});

describe('Login', function () {

    describe('Success', function () {

        it('logins with valid user credentials.', function () {
            $response = postJson(loginUrl(), loginPayload());

            $response->
                assertOk()->
                assertExactJsonStructure([
                    "message",
                    "user" => [
                        "id",
                        "email",
                    ],
                    "token"
                ])->
                assertJson([
                    'message' => 'User logged in successfully.'
                ]);

            $this->
                assertDatabaseHas('personal_access_tokens', [
                    'tokenable_id' => $response->json('user.id')
                ]);

            expect(
                User::findOrFail($response->json('user.id'))->value('last_login_at')
            )->not->toBe($response->json('user.last_login_at'));
        });
    });

    describe('Validation Failure', function () {

        it('rejects an invalid email.', function () {
            $response = postJson(loginUrl(), loginPayload([
                'email' => 'userexamplecom'
            ]));

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(["email"]);
        });

        it('rejects an email longer than 255 chars.', function () {
            $response = postJson(loginUrl(), loginPayload([
                'email' => str_repeat('user', 70) . '@example.com'
            ]));

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['email']);
        });

        it('rejects passwords shorter than 8 chars.', function () {
            $response = postJson(loginUrl(), loginPayload([
                'password' => '1234567',
            ]));

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['password']);
        });

        it('rejects login for a not existing email.', function () {
            $response = postJson(loginUrl(), loginPayload([
                'email' => 'userX@example.com',
            ]));

            $response->
                assertUnauthorized()->
                assertJson([
                    'message' => 'The provided credentials are not valid.'
                ]);
        });

        it('rejects login for a mismatched password with an email.', function () {
            $response = postJson(loginUrl(), loginPayload([
                'password' => 'stringstX',
            ]));

            $response->
                assertUnauthorized()->
                assertJson([
                    'message' => 'The provided credentials are not valid.'
                ]);
        });
    });
});
<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use function Pest\Laravel\patchJson;
use App\Features\Account\Models\User;

uses(RefreshDatabase::class);

function updateAuthPayload(array $overrides = []) : array {
    return array_merge([
        "email" => "NEWuser@example.com",
        "password" => "NEWstringst",
        "password_confirmation" => "NEWstringst"
    ], $overrides);
}
function updateAuthUrl(): string {
    return route('accounts.auth.update');
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->token = $this->user->createToken('api')->plainTextToken;

    $this->secondUser = User::factory()->create();
});

describe('UpdateAuth', function () {

    describe('Success', function () {

        it('updates user auth credentials with a valid access token.', function () {
            $response = patchJson(
                updateAuthUrl(),
                updateAuthPayload(),
                generateAuthHeader($this->token)
            );

            $response->
                assertOk()->
                assertExactJsonStructure([
                    "message",
                    "user" => [
                        "id",
                        "email",
                        "email_verified_at",
                        "last_login_at",
                        "created_at",
                        "updated_at",
                        "deleted_at",
                    ]
                ])->
                assertJson([
                    'message' => 'User informations updated successfully.'
                ]);

            $this->user->refresh();
            
            // check for new email
            $this->
                assertDatabaseHas('users', [
                    'id' => $this->user->id,
                    'email' => strtolower(updateAuthPayload()['email']),
                ]);
            
            // check for new password
            expect(
                Hash::check(updateAuthPayload()['password'], $this->user->password)
            )->toBeTrue();
        });
    });

    describe('Validation Failure', function () {

        it('rejects an invalid email.', function () {
            $response = patchJson(
                updateAuthUrl(),
                updateAuthPayload([
                    'email' => 'userexamplecom'
                ]),
                generateAuthHeader($this->token)
            );

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(["email"]);
        });

        it('rejects an email longer than 255 chars.', function () {
            $response = patchJson(
                updateAuthUrl(),
                updateAuthPayload([
                    'email' => str_repeat('user', 70) . '@example.com'
                ]),
                generateAuthHeader($this->token)
            );

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['email']);
        });

        it('rejects passwords shorter than 8 chars.', function () {
            $response = patchJson(
                updateAuthUrl(),
                updateAuthPayload([
                    'password' => '1234567',
                    'password_confirmation' => '1234567'
                ]),
                generateAuthHeader($this->token)
            );

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['password']);
        });

        it('rejects two mismatched passwords.', function () {
            $response = patchJson(
                updateAuthUrl(),
                updateAuthPayload([
                    'password' => 'stringst',
                    'password_confirmation' => 'stringstX'
                ]),
                generateAuthHeader($this->token)
            );

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['password']);
        });

        it('rejects an email already used by another user.', function () {
            $response = patchJson(
                updateAuthUrl(),
                updateAuthPayload([
                    'email' => $this->secondUser->email,
                ]),
                generateAuthHeader($this->token)
            );

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['email']);
        });
    });

    describe('Authentication Failure', function () {

        it('rejects an invalid token.', function () {
            $response = patchJson(
                updateAuthUrl(),
                updateAuthPayload(),
                generateAuthHeader($this->token . 'X')
            );

            $response->
                assertUnauthorized()->
                assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });

        it('rejects a request without an access token.', function () {
            $response = patchJson(
                updateAuthUrl(),
                updateAuthPayload(),
            );

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });
    });
});
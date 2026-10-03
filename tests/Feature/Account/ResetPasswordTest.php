<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use function Pest\Laravel\postJson;
use App\Features\Account\Models\User;

uses(RefreshDatabase::class);

function resetPasswordPayload(array $overrides = []) : array {
    return array_merge([
        "token" => "string",
        "email" => "user@example.com",
        "password" => "NEWstringst",
        "password_confirmation" => "NEWstringst"
    ], $overrides);
}
function resetPasswordUrl(): string {
    return route('accounts.auth.reset-password');
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    // create reset token
    $this->token = Password::broker()->createToken($this->user);
});

describe('ResetPassword', function () {

    describe('Success', function () {

        it('resets the user password with valid credentials.', function () {
            $response = postJson(resetPasswordUrl(), resetPasswordPayload([
                'token' => $this->token
            ]));

            $response->
                assertOk()->
                assertExactJsonStructure([
                    'message',
                    'user' => [
                        'id',
                        'email'
                    ]
                ])->
                assertJson([
                    'message' => 'Your password has been reset.'
                ]);

            $this->user->refresh();

            // check for equality of new password with database (whether it has been stored or not)
            expect(
                Hash::check(resetPasswordPayload()['password'], $this->user->password)
            )->toBeTrue();
        });
    });

    describe('Validation Failure', function () {

        it('rejects an invalid token.', function () {
            $response = postJson(resetPasswordUrl(), resetPasswordPayload([
                'token' => $this->token . 'X'
            ]));

            $response->
                assertUnprocessable()->
                assertJson([
                    'message' => 'The credentials are not valid.'
                ]);
        });

        it('rejects an invalid email.', function () {
            $response = postJson(resetPasswordUrl(), resetPasswordPayload([
                'email' => 'userexamplecom'
            ]));

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(["email"]);
        });

        it('rejects an email longer than 255 chars.', function () {
            $response = postJson(resetPasswordUrl(), resetPasswordPayload([
                'email' => str_repeat('user', 70) . '@example.com'
            ]));

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['email']);
        });

        it('rejects a not existing email.', function () {
            $response = postJson(resetPasswordUrl(), resetPasswordPayload([
                'email' => 'userX@example.com',
            ]));

            $response->
                assertUnprocessable()->
                assertJson([
                    'message' => 'The credentials are not valid.'
                ]);
        });

        it('rejects passwords shorter than 8 chars.', function () {
            $response = postJson(resetPasswordUrl(), resetPasswordPayload([
                'password' => '1234567',
                'password_confirmation' => '1234567'
            ]));

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['password']);
        });

        it('rejects two mismatched passwords.', function () {
            $response = postJson(resetPasswordUrl(), resetPasswordPayload([
                'password' => 'stringst',
                'password_confirmation' => 'stringstX'
            ]));

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['password']);
        });
    });
});
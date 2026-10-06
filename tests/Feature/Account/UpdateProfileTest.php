<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\patchJson;
use App\Features\Account\Models\User;

uses(RefreshDatabase::class);

function updateProfilePayload(array $overrides = []): array {
    return array_merge([
        "username" => "NEWstring",
        "full_name" => "NEWstring",
        "bio" => "NEWstring",
        "avatar" => "NEWstring"
    ], $overrides);
}
function updateProfileUrl(): string {
    return route('accounts.profile.update');
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->token = $this->user->createToken('api')->plainTextToken;

    $this->secondUser = User::factory()->create();
});

describe('UpdateProfile', function () {

    describe('Success', function () {

        it('updates the user profile with a valid access token.', function () {
            $response = patchJson(
                updateProfileUrl(),
                updateProfilePayload(),
                generateAuthHeader($this->token)
            );

            $response->
                assertOk()->
                assertExactJsonStructure([
                    "message",
                    "profile" => [
                        "id",
                        "username",
                        "full_name",
                        "bio",
                        "avatar",
                        "created_at",
                        "updated_at",
                        "deleted_at",
                    ]
                ])->
                assertJson([
                    'message' => 'Profile updated successfully.',
                    'profile' => [
                        'username' => strtolower('NEWstring'),
                        'full_name' => 'NEWstring',
                        'bio' => 'NEWstring',
                        'avatar' => 'NEWstring',
                    ],
                ]);
                
                $this->assertDatabaseHas('users', [
                    'id' => $this->user->id,
                    'username' => strtolower('NEWstring'),
                    'full_name' => 'NEWstring',
                    'bio' => 'NEWstring',
                    'avatar' => 'NEWstring',
                ]);
        });
    });

    describe('Authentication Failure', function () {

        it('rejects an invalid token.', function () {
            $response = patchJson(
                updateProfileUrl(),
                updateProfilePayload(),
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
                updateProfileUrl(),
                updateProfilePayload()
            );

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });
    });

    describe('Validation Failure', function () {

        it('rejects a username longer than 255 characters.', function () {
            $response = patchJson(
                updateProfileUrl(),
                updateProfilePayload([
                    'username' => str_repeat('string', 50)
                ]),
                generateAuthHeader($this->token)
            );

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['username']);
        });

        it('rejects a full name longer than 255 characters.', function () {
            $response = patchJson(
                updateProfileUrl(),
                updateProfilePayload([
                    'full_name' => str_repeat('string', 50)
                ]),
                generateAuthHeader($this->token)
            );

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['full_name']);
        });

        it('rejects an username already used by another user.', function () {
            $response = patchJson(
                updateProfileUrl(),
                updateProfilePayload([
                    'username' => $this->secondUser->username,
                ]),
                generateAuthHeader($this->token)
            );

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['username']);
        });
    });
});
<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\postJson;
use App\Features\Account\Models\User;

uses(RefreshDatabase::class);

function generateAuthHeader(string $token): array {
    return [
        'Authorization' => 'Bearer ' . $token
    ];
}
function logoutUrl(): string {
    return route('accounts.auth.logout');
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->token = $this->user->createToken('api')->plainTextToken;
});

describe('Logout', function () {

    describe('Success', function () {

        it('logs out with a valid user access token.', function () {
            // get the access token record associated with the current plain-text token.
            $accessToken = $this->user->tokens()->latest()->first();

            $response = postJson(logoutUrl(), [], generateAuthHeader($this->token));

            $response->
                assertOk()->
                assertExactJsonStructure([
                    "message",
                    "user" => [
                        "id",
                        "email",
                    ],
                ])->
                assertJson([
                    'message' => 'User logged out successfully.'
                ]);
            
            $this->
                assertDatabaseMissing('personal_access_tokens', [
                    'id' => $accessToken->id
                ]);
        });
    });

    describe('Authentication Failure', function () {

        it('rejects an invalid token.', function () {
            $response = postJson(logoutUrl(), [], generateAuthHeader($this->token . 'X'));

            $response->
                assertUnauthorized()->
                assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });

        it('rejects a request without an access token.', function () {
            $response = postJson(logoutUrl());

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });
    });
});
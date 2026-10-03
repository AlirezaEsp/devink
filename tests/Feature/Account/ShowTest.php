<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\getJson;
use App\Features\Account\Models\User;

uses(RefreshDatabase::class);

function showUrl(): string {
    return route('accounts.auth.show');
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->token = $this->user->createToken('api')->plainTextToken;
});

describe('Show', function () {

    describe('Success', function () {

        it('shows a user authentication resource with a valid access token.', function () {
            $response = getJson(showUrl(), generateAuthHeader($this->token));

            $response->
                assertOk()->
                assertExactJsonStructure([
                    "id",
                    "email",
                    "email_verified_at",
                    "last_login_at",
                    "created_at",
                    "updated_at",
                    "deleted_at",
                ])->
                assertJson([
                    'id' => $this->user->id,
                    'email' => $this->user->email,
                ]);
        });
    });

    describe('Authentication Failure', function () {

        it('rejects an invalid token.', function () {
            $response = getJson(showUrl(), generateAuthHeader($this->token . 'X'));

            $response->
                assertUnauthorized()->
                assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });

        it('rejects a request without an access token.', function () {
            $response = getJson(showUrl());

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });
    });
});
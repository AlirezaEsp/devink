<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\getJson;
use App\Features\Account\Models\User;

uses(RefreshDatabase::class);

function showProfileUrl(): string {
    return route('accounts.profile.show');
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->token = $this->user->createToken('api')->plainTextToken;
});

describe('ShowProfile', function () {

    describe('Success', function () {

        it('returns the user profile with a valid access token.', function () {
            $response = getJson(showProfileUrl(), generateAuthHeader($this->token));

            $response->
                assertOk()->
                assertExactJsonStructure([
                    "id",
                    "username",
                    "full_name",
                    "bio",
                    "avatar",
                    "created_at",
                    "updated_at",
                    "deleted_at",
                ])->
                assertJson([
                    'id' => $this->user->id,
                    'username' => $this->user->username,
                ]);
        });
    });

    describe('Authentication Failure', function () {

        it('rejects an invalid token.', function () {
            $response = getJson(showProfileUrl(), generateAuthHeader($this->token . 'X'));

            $response->
                assertUnauthorized()->
                assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });

        it('rejects a request without an access token.', function () {
            $response = getJson(showProfileUrl());

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });
    });
});
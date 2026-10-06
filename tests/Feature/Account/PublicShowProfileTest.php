<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\getJson;
use App\Features\Account\Models\User;

uses(RefreshDatabase::class);

function publicShowProfileUrl(string $username): string {
    return route('accounts.profile.show.public', ['username' => $username]);
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();
});

describe('PublicShowProfile', function () {

    describe('Success', function () {

        it('returns the user public profile with a valid username.', function () {
            $response = getJson(publicShowProfileUrl($this->user->username));

            $response->
                assertOk()->
                assertExactJsonStructure([
                    "username",
                    "full_name",
                    "bio",
                    "avatar",
                ])->
                assertJson([
                    'username' => $this->user->username,
                ]);
        });
    });

    describe('Not Found Failure', function () {

        it('returns a not found error when an invalid username passed.', function () {
            $response = getJson(publicShowProfileUrl($this->user->username . 'X'));

            $response->
                assertNotFound();
        });
    });
});
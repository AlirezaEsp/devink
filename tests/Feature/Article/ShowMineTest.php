<?php

use App\Features\Account\Models\User;
use App\Features\Article\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

function showMineArticleUrl(): string {
    return route('articles.show.mine');
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->token = $this->user->createToken('api')->plainTextToken;
});

describe('ShowMine', function () {

    describe('Success', function () {
    
        it('Fetches my articles successfully.', function () {
            Article::factory()
                ->count(2)
                ->for($this->user)
                ->create();

            $response = getJson(
                showMineArticleUrl(),
                generateAuthHeader($this->token)
            );

            $response
                ->assertOk()
                ->assertExactJsonStructure([
                    "data" => [
                        "*" => [
                        "id",
                        "title",
                        "slug",
                        ]
                    ],
                    "links" => [
                        "first",
                        "last",
                        "prev",
                        "next"
                    ],
                    "meta" => [
                        "current_page",
                        "from",
                        "last_page",
                        "links" => [
                            "*" => [
                                "url",
                                "label",
                                "page",
                                "active"
                            ]
                        ],
                        "path",
                        "per_page",
                        "to",
                        "total"
                    ]
                ]);
        });
    });

    describe('Authentication Failure', function () {

        it('rejects an invalid token.', function () {
            $response = getJson(showMineArticleUrl(), generateAuthHeader($this->token . 'X'));

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });

        it('rejects a request without an access token.', function () {
            $response = getJson(showMineArticleUrl());

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });
    });
});
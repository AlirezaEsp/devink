<?php

use App\Features\Account\Models\User;
use App\Features\Article\Enums\ArticleStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

function storePayload(array $overrides = []) : array {
    return array_merge([
        "title" => "string",
        "slug" => "string",
        "body" => "string"
    ], $overrides);
}
function storeUrl(): string {
    return route('articles.store');
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->token = $this->user->createToken('api')->plainTextToken;
});

describe('Store', function () {

    describe('Success', function () {
    
        it('Stores new article successfully.', function () {
            $payload = storePayload();
        
            $response = postJson(
                storeUrl(),
                storePayload(),
                generateAuthHeader($this->token)
            );

            $response
                ->assertCreated()
                ->assertExactJsonStructure([
                    'message',
                    'article' => [
                        'id',
                        'title',
                        'slug',
                    ],
                ])
                ->assertJson([
                    'message' => 'Article created successfully.',
                    'article' => [
                        'title' => $payload['title'],
                        'slug' => $payload['slug'],
                    ],
                ]);
            
            $this->assertDatabaseHas('articles', [
                'user_id' => $this->user->id,
                'title' => $payload['title'],
                'slug' => $payload['slug'],
                'body' => $payload['body'],
                'status' => ArticleStatus::Draft
            ]);
        });
    });

    describe('Authentication Failure', function () {

        it('rejects an invalid token.', function () {
            $response = postJson(storeUrl(), [], generateAuthHeader($this->token . 'X'));

            $response->
                assertUnauthorized()->
                assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });

        it('rejects a request without an access token.', function () {
            $response = postJson(storeUrl());

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });
    });

    describe('Validation Failure', function () {

        it('rejects an title longer than 255 chars.', function () {
            $response = postJson(
                storeUrl(),
                storePayload([
                    'title' => str_repeat('string', 70)
                ]),
                generateAuthHeader($this->token)
            );

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['title']);
        });
    });

    describe('Duplication Failure', function () {

        it('rejects storing an existing slug.', function () {
            postJson(
                storeUrl(),
                storePayload(),
                generateAuthHeader($this->token)
            );

            $response = postJson(
                storeUrl(),
                storePayload(),
                generateAuthHeader($this->token)
            );

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['slug']);
        });
    });
});
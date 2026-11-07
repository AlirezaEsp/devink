<?php

use App\Features\Account\Models\User;
use App\Features\Article\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\patchJson;

uses(RefreshDatabase::class);

function updateArticlePayload(array $overrides = []) : array {
    return array_merge([
        "title" => "stringUPDATED",
        "slug" => "stringUPDATED",
        "body" => "stringUPDATED"
    ], $overrides);
}
function updateArticleUrl(int $articleId): string {
    return route('articles.update', ['id' => $articleId]);
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->token = $this->user->createToken('api')->plainTextToken;

    $this->article = Article::factory()
        ->for($this->user)
        ->create();
});

describe('Update', function () {

    describe('Success', function () {
    
        it('Updates an existing article successfully.', function () {
            $payload = updateArticlePayload();
        
            $response = patchJson(
                updateArticleUrl($this->article->id),
                $payload,
                generateAuthHeader($this->token)
            );

            $response
                ->assertOk()
                ->assertExactJsonStructure([
                    'message',
                    'article' => [
                        'id',
                        'title',
                        'slug',
                    ],
                ])
                ->assertJson([
                    'message' => 'Article updated successfully.',
                    'article' => [
                        'id' => $this->article->id,
                        'title' => $payload['title'],
                        'slug' => $payload['slug'],
                    ],
                ]);
            
            $this->assertDatabaseHas('articles', [
                'id' => $this->article->id,
                'title' => $payload['title'],
                'slug' => $payload['slug'],
                'body' => $payload['body'],
            ]);
        });
    });

    describe('Authentication Failure', function () {

        it('rejects an invalid token.', function () {
            $response = patchJson(
                updateArticleUrl($this->article->id),
                [],
                generateAuthHeader($this->token . 'X')
            );

            $response->
                assertUnauthorized()->
                assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });

        it('rejects a request without an access token.', function () {
            $response = patchJson(updateArticleUrl($this->article->id));

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });
    });

    describe('Validation Failure', function () {

        it('rejects an title longer than 255 chars.', function () {
            $response = patchJson(
                updateArticleUrl($this->article->id),
                updateArticlePayload([
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

        it('rejects updating an article with an existing slug.', function () {
            $anotherArticle = $this->user->articles()->create([
                "title" => "stringANOTHER",
                "slug" => "stringANOTHER",
                "body" => "stringANOTHER"
            ]);

            $response = patchJson(
                updateArticleUrl($this->article->id),
                updateArticlePayload([
                    'slug' => 'stringANOTHER'
                ]),
                generateAuthHeader($this->token)
            );

            $response->
                assertUnprocessable()->
                assertJsonValidationErrors(['slug']);
        });
    });
});
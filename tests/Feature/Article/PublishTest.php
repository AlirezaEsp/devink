<?php

use App\Features\Account\Models\User;
use App\Features\Article\Enums\ArticleStatus;
use App\Features\Article\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

function publishArticleUrl(int $articleId): string {
    return route('articles.publish', ['id' => $articleId]);
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->token = $this->user->createToken('api')->plainTextToken;

    $this->article = Article::factory()
        ->for($this->user)
        ->create();
});

describe('Publish', function () {

    describe('Success', function () {
    
        it('Publishes an article successfully.', function () {
            $response = postJson(
                publishArticleUrl($this->article->id),
                [],
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
                    'message' => 'Article published successfully.',
                ]);
            
            $this->assertDatabaseHas('articles', [
                'id' => $this->article->id,
                'status' => ArticleStatus::Published,
            ]);

            $this->assertNotNull($this->article->fresh()->published_at);
        });
    });

    describe('Authentication Failure', function () {

        it('rejects an invalid token.', function () {
            $response = postJson(
                publishArticleUrl($this->article->id),
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
            $response = postJson(publishArticleUrl($this->article->id));

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });
    });

    describe('Duplication Failure', function () {

        it('rejects publishing a published article.', function () {
            $this->article->forceFill([
                'status' => ArticleStatus::Published
            ])->save();

            $response = postJson(
                publishArticleUrl($this->article->id),
                [],
                generateAuthHeader($this->token)
            );

            $response
                ->assertConflict()
                ->assertJson([
                'message' => 'Article has already been published.',
            ]);
        });
    });

    describe('Access Failure', function () {

        it('rejects publishing an article belonging to another user.', function () {
            $anotherUser = User::factory()->create();

            $anotherArticle = Article::factory()
                ->for($anotherUser)
                ->create();

            $response = postJson(
                publishArticleUrl($anotherArticle->id),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();

            $this->assertDatabaseHas('articles', [
                'id' => $anotherArticle->id,
                'status' => ArticleStatus::Draft->value,
                'published_at' => null,
            ]);
        });

        it('returns 404 when the article does not exist.', function () {
            $response = postJson(
                publishArticleUrl(999999),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();
        });

        it('returns 404 when trying to publish a deleted article.', function () {
            $this->article->delete();

            $response = postJson(
                publishArticleUrl($this->article->id),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();
        });
    });
});
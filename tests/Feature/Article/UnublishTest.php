<?php

use App\Features\Account\Models\User;
use App\Features\Article\Enums\ArticleStatus;
use App\Features\Article\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

function unpublishUrl(int $articleId): string {
    return route('articles.unpublish', ['id' => $articleId]);
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->token = $this->user->createToken('api')->plainTextToken;

    $this->article = Article::factory()
        ->for($this->user)
        ->published()
        ->create();
});

describe('Unpublish', function () {

    describe('Success', function () {
    
        it('Unpublishes an article successfully.', function () {
            $response = postJson(
                unpublishUrl($this->article->id),
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
                    'message' => 'Article unpublished successfully. now it is a draft.',
                ]);
            
            $this->assertDatabaseHas('articles', [
                'id' => $this->article->id,
                'status' => ArticleStatus::Draft,
            ]);

            $this->assertNull($this->article->fresh()->published_at);
        });
    });

    describe('Authentication Failure', function () {

        it('rejects an invalid token.', function () {
            $response = postJson(
                unpublishUrl($this->article->id),
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
            $response = postJson(unpublishUrl($this->article->id));

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });
    });

    describe('Duplication Failure', function () {

        it('rejects unpublishing a draft article.', function () {
            $this->article->forceFill([
                'status' => ArticleStatus::Draft
            ])->save();

            $response = postJson(
                unpublishUrl($this->article->id),
                [],
                generateAuthHeader($this->token)
            );

            $response
                ->assertConflict()
                ->assertJson([
                    'message' => 'Article is already a draft.',
                ]);
        });
    });

    describe('Access Failure', function () {

        it('rejects unpublishing an article belonging to another user.', function () {
            $anotherUser = User::factory()->create();

            $anotherArticle = Article::factory()
                ->for($anotherUser)
                ->published()
                ->create();

            $response = postJson(
                unpublishUrl($anotherArticle->id),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();

            $this->assertDatabaseHas('articles', [
                'id' => $anotherArticle->id,
                'status' => ArticleStatus::Published,
            ]);

            $this->assertNotNull($anotherArticle->fresh()->published_at);
        });

        it('returns 404 when the article does not exist.', function () {
            $response = postJson(
                unpublishUrl(999999),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();
        });

        it('returns 404 when trying to unpublish a deleted article.', function () {
            $this->article->delete();

            $response = postJson(
                unpublishUrl($this->article->id),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();
        });
    });
});
<?php

use App\Features\Account\Models\User;
use App\Features\Article\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

function previewUrl(int $articleId): string {
    return route('articles.preview', ['id' => $articleId]);
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->token = $this->user->createToken('api')->plainTextToken;

    $this->article = Article::factory()
        ->for($this->user)
        ->create();
});

describe('Preview', function () {

    describe('Success', function () {
    
        it('Fetches an article preview successfully.', function () {
            $response = getJson(
                previewUrl($this->article->id),
                generateAuthHeader($this->token)
            );

            $response
                ->assertOk()
                ->assertExactJsonStructure([
                    "id",
                    "author",
                    "title",
                    "slug",
                    "body",
                    "status",
                    "created_at",
                    "updated_at",
                    "published_at"
                ])
                -> assertJson([
                    'id' => $this->article->id,
                    'title' => $this->article->title,
                    'slug' => $this->article->slug,
                    'body' => $this->article->body,
                ]);
        });
    });

    describe('Authentication Failure', function () {

        it('rejects an invalid token.', function () {
            $response = getJson(
                previewUrl($this->article->id),
                generateAuthHeader($this->token . 'X')
            );

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });

        it('rejects a request without an access token.', function () {
            $response = getJson(previewUrl($this->article->id));

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });
    });

    describe('Access Failure', function () {

        it('rejects previewing an article belonging to another user.', function () {
            $anotherUser = User::factory()->create();

            $anotherArticle = Article::factory()
                ->for($anotherUser)
                ->create();

            $response = getJson(
                previewUrl($anotherArticle->id),
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();
        });

        it('rejects previewing a non-existing article.', function () {
            $response = getJson(
                previewUrl(999999),
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();
        });

        it('rejects previewing a deleted article.', function () {
            $this->article->delete();

            $response = getJson(
                previewUrl($this->article->id),
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();
        });
    });
});
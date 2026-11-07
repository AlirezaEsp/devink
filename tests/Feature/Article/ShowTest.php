<?php

use App\Features\Account\Models\User;
use App\Features\Article\Enums\ArticleStatus;
use App\Features\Article\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

function showArticleUrl(string $authorUsername, string $articleSlug): string {
    return route('articles.show', ['username' => $authorUsername, 'slug' => $articleSlug]);
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->article = Article::factory()
        ->for($this->user)
        ->published()
        ->create();
});

describe('Show', function () {

    describe('Success', function () {
    
        it('shows a published article successfully.', function () {
            $response = getJson(
                showArticleUrl(
                    $this->user->username,
                    $this->article->slug
                ),
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
                ->assertJson([
                    'id' => $this->article->id,
                    'title' => $this->article->title,
                    'slug' => $this->article->slug,
                    'body' => $this->article->body,
                    'status' => ArticleStatus::Published->value,
                    'published_at' => $this->article->published_at->toISOString()
                ]);
        });

        it('shows an article belonging to another author.', function () {
            $anotherUser = User::factory()->create();

            $anotherArticle = Article::factory()
                ->for($anotherUser)
                ->published()
                ->create();

            $response = getJson(
                showArticleUrl($anotherUser->username, $anotherArticle->slug)
            );

            $response
                ->assertOk()
                ->assertJson([
                    'id' => $anotherArticle->id,
                    'title' => $anotherArticle->title,
                    'author' => $anotherUser->username
                ]);
        });
    });

    describe('Validation Failure', function () {

        it('returns 404 when the author does not exist.', function () {
            $response = getJson(
                showArticleUrl('unknown-user', $this->article->slug)
            );

            $response->assertNotFound();
        });

        it('returns 404 when the article does not exist.', function () {
            $response = getJson(
                showArticleUrl($this->user->username, 'unknown-article')
            );

            $response->assertNotFound();
        });

        it('returns 404 when the article does not belong to the specified author.', function () {
            $anotherUser = User::factory()->create();

            $response = getJson(
                showArticleUrl($anotherUser->username, $this->article->slug)
            );

            $response->assertNotFound();
        });
    });

    describe('Access Failure', function () {

        it('rejects showing a drafted article.', function () {
            $this->article->forceFill([
                'status' => ArticleStatus::Draft,
                'published_at' => null
            ])->save();

            $response = getJson(
                showArticleUrl(
                    $this->user->username,
                    $this->article->slug
                ),
            );

            $response->assertNotFound();
        });

        it('returns 404 when the article has been deleted.', function () {
            $this->article->delete();

            $response = getJson(
                showArticleUrl($this->user->username, $this->article->slug)
            );

            $response->assertNotFound();
        });
    });
});
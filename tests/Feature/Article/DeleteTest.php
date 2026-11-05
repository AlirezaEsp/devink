<?php

use App\Features\Account\Models\User;
use App\Features\Article\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;

uses(RefreshDatabase::class);

function deleteUrl(int $articleId): string {
    return route('articles.destroy', ['id' => $articleId]);
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->token = $this->user->createToken('api')->plainTextToken;

    $this->article = Article::factory()
        ->for($this->user)
        ->create();
});

describe('Delete', function () {

    describe('Success', function () {
    
        it('Deletes an article successfully.', function () {
            $response = deleteJson(
                deleteUrl($this->article->id),
                [],
                generateAuthHeader($this->token)
            );

            $response
                ->assertOk()
                ->assertJson([
                    'message' => 'Article deleted successfully.'
                ]);
            
            assertSoftDeleted('articles', [
                'id' => $this->article->id
            ]);
        });
    });

    describe('Authentication Failure', function () {

        it('rejects an invalid token.', function () {
            $response = deleteJson(
                deleteUrl($this->article->id),
                [],
                generateAuthHeader($this->token . 'X')
            );

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });

        it('rejects a request without an access token.', function () {
            $response = deleteJson(deleteUrl($this->article->id));

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });
    });

    describe('Access Failure', function () {

        it('rejects deleting an article belonging to another user.', function () {
            $anotherUser = User::factory()->create();

            $anotherArticle = Article::factory()
                ->for($anotherUser)
                ->create();

            $response = deleteJson(
                deleteUrl($anotherArticle->id),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();

            $this->assertDatabaseHas('articles', [
                'id' => $anotherArticle->id,
                'deleted_at' => null,
            ]);
        });

        it('rejects deleting a non-existing article.', function () {
            $response = deleteJson(
                deleteUrl(999999),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();
        });

        it('rejects deleting an already deleted article.', function () {
            $this->article->delete();

            $response = deleteJson(
                deleteUrl($this->article->id),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();
        });
    });
});
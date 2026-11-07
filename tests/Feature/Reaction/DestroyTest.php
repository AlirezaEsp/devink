<?php

use App\Features\Account\Models\User;
use App\Features\Article\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\deleteJson;

uses(RefreshDatabase::class);

function destroyReactionUrl(int $articleId): string {
    return route('articles.unlike', ['id' => $articleId]);
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->token = $this->user->createToken('api')->plainTextToken;

    $this->article = Article::factory()->published()->create();

    $this->user->reactions()->create([
        'article_id' => $this->article->id
    ]);
});

describe('Destroy', function () {

    describe('Success', function () {
    
        it('removes an existing reaction for an article successfully.', function () {
            $response = deleteJson(
                destroyReactionUrl($this->article->id),
                [],
                generateAuthHeader($this->token)
            );

            $response
                ->assertOk()
                ->assertJson([
                    'message' => 'Reaction removed successfully.',
                ]);
            
            $this->assertDatabaseMissing('reactions', [
                'user_id' => $this->user->id,
                'article_id' => $this->article->id
            ]);
        });

        it('allows different users to destroy reaction for the same article.', function () {
            $anotherUser = User::factory()->create();
            $anotherToken = $anotherUser->createToken('api')->plainTextToken;

            $anotherUser->reactions()->create([
                'article_id' => $this->article->id
            ]);

            $response = deleteJson(
                destroyReactionUrl($this->article->id),
                [],
                generateAuthHeader($anotherToken)
            );

            $response->assertOk();

            $this->assertDatabaseMissing('reactions', [
                'user_id' => $anotherUser->id,
                'article_id' => $this->article->id,
            ]);

            $this->assertDatabaseHas('reactions', [
                'user_id' => $this->user->id,
                'article_id' => $this->article->id,
            ]);
        });
    });

    describe('Authentication Failure', function () {

        it('rejects an invalid token.', function () {
            $response = deleteJson(
                destroyReactionUrl($this->article->id),
                [],
                generateAuthHeader($this->token . 'X')
            );

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.',
                ]);
        });

        it('rejects a request without an access token.', function () {
            $response = deleteJson(destroyReactionUrl($this->article->id));

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });
    });

    describe('Missing Failure', function () {

        it('rejects removing a non-existing reaction.', function () {
            $firstResponse = deleteJson(
                destroyReactionUrl($this->article->id),
                [],
                generateAuthHeader($this->token)
            );

            $firstResponse->assertOk();

            $response = deleteJson(
                destroyReactionUrl($this->article->id),
                [],
                generateAuthHeader($this->token)
            );

            $response
                ->assertConflict()
                ->assertJson([
                    'message' => 'Reaction does not exist.',
                ]);

            $this->assertDatabaseCount('reactions', 0);
        });
    });

    describe('Access Failure', function () {

        it('does not allow removing a reaction from a draft article.', function () {
            $article = Article::factory()->create();

            $this->user->reactions()->create([
                'article_id' => $article->id
            ]);

            $response = deleteJson(
                destroyReactionUrl($article->id),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();

            $this->assertDatabaseHas('reactions', [
                'user_id' => $this->user->id,
                'article_id' => $article->id,
            ]);
        });

        it('returns not found when trying to remove a reaction from a non-existing article.', function () {
            $response = deleteJson(
                destroyReactionUrl(999999),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();
        });
    });
});
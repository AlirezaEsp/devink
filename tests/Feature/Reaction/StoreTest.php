<?php

use App\Features\Account\Models\User;
use App\Features\Article\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

function storeReactionUrl(int $articleId): string {
    return route('articles.like', ['id' => $articleId]);
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->token = $this->user->createToken('api')->plainTextToken;

    $this->article = Article::factory()->published()->create();
});

describe('Store', function () {

    describe('Success', function () {
    
        it('stores new reaction for an article successfully.', function () {
            $response = postJson(
                storeReactionUrl($this->article->id),
                [],
                generateAuthHeader($this->token)
            );

            $response
                ->assertCreated()
                ->assertJson([
                    'message' => 'Reaction added successfully.',
                ]);
            
            $this->assertDatabaseHas('reactions', [
                'user_id' => $this->user->id,
                'article_id' => $this->article->id
            ]);
        });

        it('allows different users to react to the same article.', function () {
            $anotherUser = User::factory()->create();
            $anotherToken = $anotherUser->createToken('api')->plainTextToken;

            $response = postJson(
                storeReactionUrl($this->article->id),
                [],
                generateAuthHeader($anotherToken)
            );

            $response->assertCreated();

            $this->assertDatabaseHas('reactions', [
                'user_id' => $anotherUser->id,
                'article_id' => $this->article->id,
            ]);
        });
    });

    describe('Authentication Failure', function () {

        it('rejects an invalid token.', function () {
            $response = postJson(
                storeReactionUrl($this->article->id),
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
            $response = postJson(storeReactionUrl($this->article->id));

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });
    });

    describe('Duplication Failure', function () {

        it('rejects storing an existing reaction.', function () {
            $firstResponse = postJson(
                storeReactionUrl($this->article->id),
                [],
                generateAuthHeader($this->token)
            );

            $firstResponse->assertCreated();

            $response = postJson(
                storeReactionUrl($this->article->id),
                [],
                generateAuthHeader($this->token)
            );

            $response->
                assertConflict()->
                assertJson([
                    'message' => 'Reaction already exists.'
                ]);
            
            $this->assertDatabaseCount('reactions', 1);
        });
    });

    describe('Access Failure', function () {

        it('rejects reacting to a draft article.', function () {
            $article = Article::factory()->create();

            $response = postJson(
                storeReactionUrl($article->id),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();

            $this->assertDatabaseMissing('reactions', [
                'user_id' => $this->user->id,
                'article_id' => $article->id,
            ]);
        });

        it('rejects reacting to a non-existing article.', function () {
            $response = postJson(
                storeReactionUrl(999999),
                [],
                generateAuthHeader($this->token)
            );

            $response->assertNotFound();
        });
    });
});
<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use function Pest\Laravel\postJson;
use App\Features\Account\Models\User;

uses(RefreshDatabase::class);

function resendUrl(): string {
    return route('accounts.auth.verification.resend');
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();

    $this->token = $this->user->createToken('api')->plainTextToken;
});

describe('ResendVerification', function () {

    describe('Success', function () {

        it('sends a verification email to an unverified user.', function () {
            Notification::fake();

            postJson(resendUrl(), [], generateAuthHeader($this->token))->
                assertOk()->
                assertJson([
                    'message' => 'Verification email sent.'
                ]);

            Notification::assertSentTo(
                $this->user,
                VerifyEmail::class
            );
        });
    });

    describe('Authentication Failure', function () {

        it('rejects an invalid token.', function () {
            $response = postJson(resendUrl(), [], generateAuthHeader($this->token . 'X'));

            $response->
                assertUnauthorized()->
                assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });

        it('rejects a request without an access token.', function () {
            $response = postJson(resendUrl());

            $response
                ->assertUnauthorized()
                ->assertJson([
                    'message' => 'Unauthenticated.'
                ]);
        });
    });

    describe('Already Verified', function () {
        it('rejects a request via conflict error when the user is already verified.', function () {
            $this->user->forceFill([
                'email_verified_at' => now()
            ])->save();

            postJson(resendUrl(), [], generateAuthHeader($this->token))->
                assertConflict()->
                assertJson([
                    'message' => 'The email has already been verified.'
                ]);
        });
    });
});
<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use function Pest\Laravel\getJson;
use App\Features\Account\Models\User;

uses(RefreshDatabase::class);

function verifyUrl(User $user, int $id = null, string $email = null): string {
    return URL::temporarySignedRoute(
        'accounts.auth.verification.verify',
        now()->addMinutes(),
        [
            'id' => $id ? $id : $user->id,
            'hash' => $email ? sha1($email) : sha1($user->getEmailForVerification())
        ]
    );
}

beforeEach(function () {
    $this->user = User::factory()->defaultTestingUser()->create();
});

describe('VerifyEmail', function () {

    describe('Success', function () {

        it('verifies the user email with a valid verification URL.', function () {
            getJson(verifyUrl($this->user))->
                assertOk()->
                assertJson([
                    'message' => 'Email verified successfully.'
                ]);

            expect(
                $this->user->fresh()->email_verified_at
            )->
                not->toBeNull();
        });
    });

    describe('Failure', function () {

        it('rejects a request via forbidden error when the verification hash is invalid.', function () {
            getJson(verifyUrl($this->user, email: 'userX@example.com'))->
                assertForbidden()->
                assertJson([
                    'message' => 'The verification link is invalid.'
                ]);

            expect(
                $this->user->fresh()->email_verified_at
            )->
                toBeNull();
        });

        it('returns OK when the the email is already verified.', function () {
            $this->user->forceFill([
                'email_verified_at' => now()
            ])->save();

            getJson(verifyUrl($this->user))->
                assertOk()->
                assertJson([
                    'message' => 'Email verified successfully.'
                ]);
        });

        it('rejects a request via not found error when the user not exists.', function () {
            getJson(verifyUrl($this->user, id: 999))->
                assertNotFound();
        });

        it('rejects a request via forbidden error when URL signature is invalid.', function () {
            $invalidUrl = verifyUrl($this->user) . '&tampered=true';
        
            getJson($invalidUrl)->
                assertForbidden()->
                assertJson([
                    'message' => 'The verification link is invalid or has expired.'
                ]);
        });
    });
});
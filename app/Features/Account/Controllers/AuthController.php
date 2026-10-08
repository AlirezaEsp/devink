<?php

namespace App\Features\Account\Controllers;

use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Features\Account\Models\User;
use App\Features\Account\Requests\RegisterRequest;
use App\Features\Account\Requests\LoginRequest;
use App\Features\Account\Requests\UpdateUserRequest;
use App\Features\Account\Responses\UpdateUserResponse;
use App\Features\Account\Resources\UserDetailedResource;
use App\Features\Account\Resources\UserResource;
use App\Features\Account\Requests\ForgotPasswordRequest;
use App\Features\Account\Requests\ResetPasswordRequest;

/**
 * AuthController
 * 
 * Controlls Authentiction flows
 */
class AuthController
{
    /**
     * Register
     * 
     * @param RegisterRequest $request
     *
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // get new user register (validated) data from request
        $registerData = $request->validated();

        // replace password with hash one
        $registerData['password'] = Hash::make($registerData['password']);

        // create new user in db
        $registeredUser = User::create($registerData);

        // trigger Registered event for verification notification
        event(new Registered($registeredUser));

        // return successful message along the registered user information
        return response()->json([
            'message' => 'User registered successfully. Please verify your email.',
            'user' => new UserDetailedResource($registeredUser)
        ], 201);
    }

    /**
     * VerifyEmail
     *
     * @param int $id user id from path parameter
     * @param string $hash user email hash from path parameter
     *
     * @return JsonResponse
     */
    #[QueryParameter(
        'expires',
        description: 'The expiration timestamp of the signed verification URL.',
        type: 'integer',
        required: true,
    )]
    #[QueryParameter(
        'signature',
        description: 'The signature of the verification URL.',
        type: 'string',
        required: true,
    )]    
    public function verify(int $id, string $hash): JsonResponse {
        // find user
        $user = User::findOrFail($id);

        // check whether user email already verified or not
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'The email has already been verified.'
            ], 409);
        }

        // check user email with hash version
        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            // --- here needs a refactor to have an specialized exception
            throw new HttpException(
                403,
                'The verification link is invalid.'
            );
        }

        // store user verified_at field for now
        $user->markEmailAsVerified();

        // return successful message
        return response()->json([
            'message' => 'Email verified successfully.'
        ]);
    }

    /**
     * Login
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // get client login (validated) credeentials from request
        $credentials = $request->validated();

        // find user
        $user = User::query()->where('email', $credentials['email'])->first();

        // check for password
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new AuthenticationException(
                'The provided credentials are not valid.'
            );
        }

        // generate token
        $token = $user->createToken('api')->plainTextToken;

        // return successful message along the logged in user info and access token
        return response()->json([
            'message' => 'User logged in successfully.',
            'user' => new UserResource($user),
            'token' => $token
        ]);
    }

    /**
     * ForgotPassword
     *
     * @param ForgotPasswordRequest $request
     *
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        // send reset password link notification
        $result = Password::sendResetLink($request->only('email'));

        // check the operation result whether it fails
        if ($result !== Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'The request is not valid.'
            ], 422);
        }

        // return successful message
        return response()->json([
            'message' => __($result)
        ]);
    }

    /**
     * ResetPassword
     *
     * @param ResetPasswordRequest $request
     *
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        // get client reset password (validated) credeentials from request
        $credentials = $request->validated();

        // define null user to assign value later
        $user = null;

        // result of password reset operation
        $result = Password::reset(
            $credentials,
            // closure
            function ($resetUser, string $password) use (&$user) : void {
                // replace user's password
                $resetUser->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                // Invalidate tokens created before the password reset.
                $resetUser->tokens()->delete();

                // assign updated user to user var
                $user = $resetUser;
            }
        );

        // return unprocessable error if process failed
        if ($result !== Password::PASSWORD_RESET) {
            return response()->json([
                'message' => "The credentials are not valid.",
            ], 422);
        }

        // return successful message along the user info
        return response()->json([
            'message' => __($result),
            'user' => new UserResource($user)
        ]);
    }

    /**
     * ResendVerification
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function resend(Request $request): JsonResponse {
        // get user
        $user = $request->user();

        // check whether user email already verified or not
        if ($user->hasVerifiedEmail()) {
            // --- here needs a refactor to have an specialized exception
            throw new HttpException(
                409,
                'The email has already been verified.'
            );
        }

        // send email verification (again)
        $user->sendEmailVerificationNotification();

        // return successful message
        return response()->json([
            'message' => 'Verification email sent.'
        ]);
    }

    /**
     * Logout
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // get user
        $user = $request->user();

        // invalidate current user access token
        $user->currentAccessToken()->delete();

        // return successful message along the user info
        return response()->json([
            'message' => 'User logged out successfully.',
            'user' => new UserResource($user)
        ]);
    }

    /**
     * Show
     *
     * @param Request $request
     *
     * @return UserDetailedResource
     */
    public function show(Request $request): UserDetailedResource
    {
        // return user auth resource
        return new UserDetailedResource($request->user());
    }

    /**
     * Update
     *
     * @param UpdateUserRequest $request
     *
     * @return UpdateUserResponse
     */
    public function update(UpdateUserRequest $request): UpdateUserResponse
    {
        // get user
        $user = $request->user();

        // get user update (validated) data from request
        $updateData = $request->validated();

        // update user data
        $user->update($updateData);

        // refresh iuser instance
        $user->refresh();

        // return successful response
        return new UpdateUserResponse($user);
    }
}

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
use App\Features\Account\Responses\RegisterResponse;
use App\Features\Account\Services\EmailVerificationService;
use App\Features\Account\Requests\LoginRequest;
use App\Features\Account\Responses\LoginResponse;
use App\Features\Account\Responses\LogoutResponse;
use App\Features\Account\Requests\UpdateUserRequest;
use App\Features\Account\Services\UpdateUserService;
use App\Features\Account\Responses\UpdateUserResponse;
use App\Features\Account\Resources\UserDetailedResource;
use App\Features\Account\Requests\ForgotPasswordRequest;
use App\Features\Account\Responses\ForgotPasswordResponse;
use App\Features\Account\Requests\ResetPasswordRequest;
use App\Features\Account\Responses\ResetPasswordResponse;

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
     * @return RegisterResponse
     */
    public function register(RegisterRequest $request): RegisterResponse
    {
        // get new user register (validated) data from request
        $registerData = $request->validated();

        // replace password with hash one
        $registerData['password'] = Hash::make($registerData['password']);

        // create new user in db
        $registeredUser = User::create($registerData);

        // trigger Registered event for verification notification
        event(new Registered($registeredUser));

        // return registered user information
        return new RegisterResponse($registeredUser);
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
                'message' => 'Email already verified.'
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

        // return successful response
        return response()->json([
            'message' => 'Email verified successfully.'
        ]);
    }

    /**
     * Login
     *
     * @param LoginRequest $request
     *
     * @return LoginResponse
     */
    public function login(LoginRequest $request): LoginResponse
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

        // return logged in user instance with access token
        return new LoginResponse([
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * ForgotPassword
     *
     * @param ForgotPasswordRequest $request
     *
     * @return mixed
     */
    public function forgotPassword(ForgotPasswordRequest $request): mixed
    {
        // send reset password link notification
        $result = Password::sendResetLink($request->only('email'));

        // check the operation result whether it fails
        if ($result !== Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'The request is not valid.'
            ], 422);
        }

        // return successful response
        return new ForgotPasswordResponse($result);
    }

    /**
     * ResetPassword
     *
     * @param ResetPasswordRequest $request
     *
     * @return mixed
     */
    public function resetPassword(ResetPasswordRequest $request): mixed
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

        // return successful response
        return new ResetPasswordResponse([
            'status' => $result,
            'user' => $user
        ]);
    }

    /**
     * ResendVerification
     *
     * @param Request $request request coming from client
     * @param EmailVerificationService $service related service
     *
     * @return JsonResponse
     */
    public function resend(Request $request, EmailVerificationService $service): JsonResponse {
        $service->resendVerificationEmail($request->user());

        return response()->json([
            'message' => 'Verification email sent.'
        ]);
    }

    /**
     * Logout
     *
     * @param Request $request
     *
     * @return LogoutResponse
     */
    public function logout(Request $request): LogoutResponse
    {
        $user = $request->user();

        $user->currentAccessToken()->delete();

        return new LogoutResponse($user);
    }

    /**
     * Show
     *
     * @param Request $request Request coming from client
     *
     * @return UserDetailedResource
     */
    public function show(Request $request): UserDetailedResource
    {
        return new UserDetailedResource($request->user());
    }

    /**
     * Update
     *
     * @param UpdateUserRequest $request Incoming request
     * @param UpdateUserService $service Related Service
     *
     * @return UpdateUserResponse
     */
    public function update(UpdateUserRequest $request, UpdateUserService $service): UpdateUserResponse
    {
        $user = $service->updateUser(
            $request->user(),
            $request->validated()
        );

        return new UpdateUserResponse($user);
    }
}

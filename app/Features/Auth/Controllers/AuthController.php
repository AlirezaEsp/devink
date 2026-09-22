<?php

namespace App\Features\Auth\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Features\Auth\Requests\RegisterRequest;
use App\Features\Auth\Services\RegisterService;
use App\Features\Auth\Responses\RegisterResponse;
use App\Features\Auth\Requests\LoginRequest;
use App\Features\Auth\Services\LoginService;
use App\Features\Auth\Responses\LoginResponse;
use App\Features\Auth\Responses\LogoutResponse;
use App\Features\Auth\Requests\UpdateUserRequest;
use App\Features\Auth\Services\UpdateUserService;
use App\Features\Auth\Responses\UpdateUserResponse;
use App\Features\Auth\Resources\UserDetailedResource;
use App\Features\Auth\Requests\ForgotPasswordRequest;
use App\Features\Auth\Services\ForgotPasswordService;
use App\Features\Auth\Responses\ForgotPasswordResponse;
use App\Features\Auth\Requests\ResetPasswordRequest;
use App\Features\Auth\Services\ResetPasswordService;
use App\Features\Auth\Responses\ResetPasswordResponse;

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
     * @param RegisterRequest $request Dedicated form request
     * @param RegisterService $service Dedicated service [DI from ServiceContainer]
     *
     * @return RegisterResponse
     */
    public function register(RegisterRequest $request, RegisterService $service): RegisterResponse
    {
        $user = $service->registerUser(
            $request->validated()
        );

        return new RegisterResponse($user);
    }
    
    /**
     * Login
     *
     * @param LoginRequest $request Dedicated form request
     * @param LoginService $service Dedicated service [DI from ServiceContainer]
     *
     * @return LoginResponse
     */
    public function login(LoginRequest $request, LoginService $service): LoginResponse
    {
        $user_array = $service->loginUser(
            $request->validated()
        );

        return new LoginResponse($user_array);
    }
    
    /**
     * ForgotPassword
     *
     * @param ForgotPasswordRequest $request Request coming from client
     *
     * @return ForgotPasswordResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request, ForgotPasswordService $service): ForgotPasswordResponse
    {
        $result = $service->forgotPassword($request->only('email'));

        return new ForgotPasswordResponse($result);
    }
    
    /**
     * ResetPassword
     *
     * @param ResetPasswordRequest $request Request coming from client
     * @param ResetPasswordService $service Related service
     *
     * @return mixed
     */
    public function resetPassword(ResetPasswordRequest $request, ResetPasswordService $service): mixed
    {
        $result = $service->resetPassword($request->validated());

        // return 422 if process failed
        if ($result['status'] !== Password::PASSWORD_RESET) {
            return response()->json([
                'message' => __("The credentials are not not valid."),
            ], 422);
        }

        return new ResetPasswordResponse($result);
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

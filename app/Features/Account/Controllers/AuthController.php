<?php

namespace App\Features\Account\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Features\Account\Models\User;
use App\Features\Account\Requests\RegisterRequest;
use App\Features\Account\Responses\RegisterResponse;
use App\Features\Account\Requests\LoginRequest;
use App\Features\Account\Services\LoginService;
use App\Features\Account\Responses\LoginResponse;
use App\Features\Account\Responses\LogoutResponse;
use App\Features\Account\Requests\UpdateUserRequest;
use App\Features\Account\Services\UpdateUserService;
use App\Features\Account\Responses\UpdateUserResponse;
use App\Features\Account\Resources\UserDetailedResource;
use App\Features\Account\Requests\ForgotPasswordRequest;
use App\Features\Account\Services\ForgotPasswordService;
use App\Features\Account\Responses\ForgotPasswordResponse;
use App\Features\Account\Requests\ResetPasswordRequest;
use App\Features\Account\Services\ResetPasswordService;
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
     * @param RegisterRequest $request Dedicated form request
     *
     * @return RegisterResponse
     */
    public function register(RegisterRequest $request): RegisterResponse
    {
        $preparedData = $request->validated();

        $preparedData['password'] = Hash::make($preparedData['password']);
        $user = User::create($preparedData);

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
     * @return mixed
     */
    public function forgotPassword(ForgotPasswordRequest $request, ForgotPasswordService $service): mixed
    {
        $result = $service->forgotPassword($request->only('email'));

        if ($result !== Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => __($result)
            ], 422);
        }

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
                'message' => __("The credentials are not valid."),
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

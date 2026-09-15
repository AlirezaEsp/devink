<?php

namespace App\Features\Auth\Controllers;

use Illuminate\Http\Request;

use App\Features\Auth\Requests\RegisterRequest;
use App\Features\Auth\Services\RegisterService;
use App\Features\Auth\Responses\RegisterResponse;

use App\Features\Auth\Requests\LoginRequest;
use App\Features\Auth\Services\LoginService;
use App\Features\Auth\Responses\LoginResponse;

use App\Features\Auth\Services\LogoutService;
use App\Features\Auth\Responses\LogoutResponse;

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
    public function store(RegisterRequest $request, RegisterService $service): RegisterResponse
    {
        $user_array = $service->registerUser(
            $request->validated()
        );

        return new RegisterResponse($user_array);
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
     * Logout
     *
     * @param Request $request
     * @param LogoutService $service Dedicated service [DI from ServiceContainer]
     *
     * @return LogoutResponse
     */
    public function logout(Request $request, LogoutService $service): LogoutResponse
    {
        $user_array = $service->logoutUser(
            $request->user()
        );

        return new LogoutResponse($user_array);
    }
}

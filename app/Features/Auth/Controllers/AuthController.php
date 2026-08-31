<?php

namespace App\Features\Auth\Controllers;

use App\Features\Auth\Requests\RegisterRequest;
use App\Features\Auth\Requests\LoginRequest;
use App\Features\Auth\Requests\LogoutRequest;
use App\Features\Auth\Services\RegisterService;
use App\Features\Auth\Services\LoginService;
use App\Features\Auth\Services\LogoutService;
use App\Features\Auth\Resources\RegisterResponse;
use App\Features\Auth\Resources\LoginResponse;
use App\Features\Auth\Resources\LogoutResponse;

/**
 * AuthController
 * 
 * Controlls Authentiction flows
 */
class AuthController
{    
    /**
     * Method store
     *
     * Controlls user registertaion flow
     * 
     * @param RegisterRequest $request Dedicated form request
     * @param RegisterService $service Dedicated service [DI from ServiceContainer]
     *
     * @return RegisterResponse
     */
    public function store(RegisterRequest $request, RegisterService $service): RegisterResponse {
        $user = $service->registerUser(
            $request->validated()
        );

        return new RegisterResponse($user);
    }
    
    /**
     * Method login
     * 
     * Controlls user logging in flow
     *
     * @param LoginRequest $request Dedicated form request
     * @param LoginService $service Dedicated service [DI from ServiceContainer]
     *
     * @return LoginResponse
     */
    public function login(LoginRequest $request, LoginService $service): LoginResponse
    {
        $user = $service->loginUser(
            $request->validated()
        );

        return new LoginResponse($user);
    }
    
    /**
     * Method logout
     * 
     * Controlls user logging out flow
     *
     * @param LogoutRequest $request Dedicated form request
     * @param LogoutService $service Dedicated service [DI from ServiceContainer]
     *
     * @return LogoutResponse
     */
    public function logout(LogoutRequest $request, LogoutService $service): LogoutResponse {
        $user = $service->logoutUser($request->user());

        return new LogoutResponse($user);
    }
}

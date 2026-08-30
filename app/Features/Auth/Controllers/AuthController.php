<?php

namespace App\Features\Auth\Controllers;

use Illuminate\Http\Request;
use App\Features\Auth\Requests\RegisterRequest;
use App\Features\Auth\Requests\LoginRequest;
use App\Features\Auth\Services\RegisterService;
use App\Features\Auth\Services\LoginService;
use App\Features\Auth\Resources\RegisterResponse;
use App\Features\Auth\Resources\LoginResponse;

class AuthController
{
    public function store(RegisterRequest $request, RegisterService $service): RegisterResponse {
        $user = $service->registerUser(
            $request->validated()
        );

        return new RegisterResponse($user);
    }

    public function login(LoginRequest $request, LoginService $service): LoginResponse
    {
        $user = $service->loginUser(
            $request->validated()
        );

        return new LoginResponse($user);
    }
}

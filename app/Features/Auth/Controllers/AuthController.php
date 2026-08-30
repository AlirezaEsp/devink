<?php

namespace App\Features\Auth\Controllers;

use Illuminate\Http\Request;
use App\Features\Auth\Requests\RegisterRequest;
use App\Features\Auth\Services\RegisterService;
use App\Features\Auth\Resources\UserResource;

class AuthController
{
    public function store(RegisterRequest $request, RegisterService $service): UserResource {
        $user = $service->registerUser(
            $request->validated()
        );

        return new UserResource($user);
    }
}

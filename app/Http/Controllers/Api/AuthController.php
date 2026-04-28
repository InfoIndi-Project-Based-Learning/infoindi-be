<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthServices;

class AuthController extends BaseApiController
{
    protected $authServices;

    public function __construct(AuthServices $authServices)
    {
        $this->authServices = $authServices;
    }

    public function register(RegisterRequest $request)
    {
        $payload = $request->only('name', 'email', 'password');
        $result = $this->authServices->register($payload);
        if (!$result) {
            return $this->error('User already exists.', 409);
        }
        return $this->created($result, 'User registered successfully.');
    }

    public function login(LoginRequest $request)
    {
        $payload = $request->only('email', 'password');
        $result = $this->authServices->login($payload);
        if (!$result) {
            return $this->error('Invalid credentials.', 401);
        }
        return $this->created($result, 'User logged in successfully.');
    }
}

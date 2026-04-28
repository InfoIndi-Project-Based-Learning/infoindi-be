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
        
        return $this->created($this->authServices->register($payload), 'User registered successfully.');
    }

    public function login(LoginRequest $request)
    {
        $payload = $request->only('email', 'password');
        
        return $this->success($this->authServices->login($payload), 'User logged in successfully.');
    }
}

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
        
        return $this->created($this->authServices->register($request->validated()), 'User registered successfully.');
    }

    public function login(LoginRequest $request)
    {
        return $this->success($this->authServices->login($request->validated()), 'User logged in successfully.');
    }

    public function logout()
    {
        auth('api')->logout();
        return $this->success(null, 'User logged out successfully.');
    }

    public function me(){
        return $this->success(auth('api')->user(), 'User data fetched successfully.');
    }
}

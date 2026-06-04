<?php

namespace App\Services;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UserBannedException;
use App\Exceptions\UserAlreadyExistException;
use App\Http\Resources\UserResource;
use App\Models\User;


class AuthServices
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register($data)
    {
        if($this->userService->findByEmail($data['email'])) {
            throw new UserAlreadyExistException();
        }

        $user = $this->userService->createUser($data);
        $token = auth('api')->login($user);
        return $this->tokenPayload($user->load('profile'), $token);
    }

    public function login($data)
    {
        if (!$token = auth('api')->attempt($data)) {
            throw new InvalidCredentialsException();
        }

        $user = auth('api')->user();
        if (!$user->is_active) {
            auth('api')->logout();
            throw new UserBannedException();
        }

        return $this->tokenPayload($user->load('profile'), $token);
    }

    public function tokenPayload(User $user, string $token)
    {
        return [
            'user' => new UserResource($user),
            'access_token' => $token
        ];
    }
}
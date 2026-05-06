<?php

namespace App\Services;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UserAlreadyExistException;
use App\Http\Resources\UserResource;
use App\Models\User;


class AuthServices
{

    public function register($data)
    {
        $userService = new UserService();

        if($userService->findByEmail($data['email'])) {
            throw new UserAlreadyExistException();
        }

        $user = $userService->createUser($data);
        $token = auth('api')->login($user);
        return $this->tokenPayload($user->load('profile'), $token);
    }

    public function login($data)
    {
        if (!$token = auth('api')->attempt($data)) {
            throw new InvalidCredentialsException();
        }

        return $this->tokenPayload(
            auth('api')
            ->user()
            ->load('profile'), 
            $token);
    }

    public function tokenPayload(User $user, string $token)
    {
        return [
            'user' => new UserResource($user),
            'access_token' => $token
        ];
    }
}
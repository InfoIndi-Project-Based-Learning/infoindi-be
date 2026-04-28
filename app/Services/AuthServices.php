<?php

namespace App\Services;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UserAlreadyExistException;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthServices
{

    public function register($data)
    {
        if(User::where('email', $data['email'])->exists()) {
            throw new UserAlreadyExistException();
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        $token = auth('api')->login($user);

        return $this->tokenPayload($user, $token);
    }

    public function login($data)
    {
        if (!$token = auth('api')->attempt($data)) {
            throw new InvalidCredentialsException();
        }

        return $this->tokenPayload(auth('api')->user(), $token);
    }

    public function tokenPayload($user, $token)
    {
        return [
            'user' => new UserResource($user),
            'access_token' => $token
        ];
    }
}
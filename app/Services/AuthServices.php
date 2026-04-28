<?php

namespace App\Services;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthServices
{

    public function register($data)
    {
        $existingUser = User::where('email', $data['email'])->first();

        if ($existingUser) {
            return null;
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
            return null;
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
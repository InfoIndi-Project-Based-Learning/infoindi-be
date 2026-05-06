<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends BaseApiController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        $users = $this->userService->getUsers($request);
        return $this->paginated($users, UserResource::class, 'Users fetched successfully.');
    }

    public function profile(User $user)
    {
        $user = $this->userService->getUserInformation($user);
        return $this->success($user, 'User information fetched successfully.');
    }

    public function follow(User $user, User $targetUser)
    {
        $this->userService->follow($user, $targetUser);
        return $this->success(null, 'User followed successfully.');
    }

    public function unfollow(User $user, User $targetUser)
    {
        $this->userService->unfollow($user, $targetUser);
        return $this->success(null, 'User unfollowed successfully.');
    }

    public function followers(User $user)
    {
        $followers = $this->userService->getFollowers($user);
        return $this->success($followers, 'Followers fetched successfully.');
    }

    public function following(User $user)
    {
        $following = $this->userService->getFollowing($user);
        return $this->success($following, 'Following fetched successfully.');
    }

    public function updateProfile(User $user, Request $request)
    {
        $user = $this->userService->updateProfile($user, $request->all());
        return $this->success($user, 'User profile updated successfully.');
    }


    public function update(User $user, Request $request)
    {
        $user = $this->userService->update($user, $request->all());
        return $this->success($user, 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->userService->delete($user);
        return $this->success(null, 'User deleted successfully.');
    }
}

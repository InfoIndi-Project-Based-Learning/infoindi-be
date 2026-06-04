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
        return $this->success(new UserResource($user), 'User information fetched successfully.');
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

    public function likedPosts(User $user)
    {
        $posts = $user->likedPosts()->with(['category', 'user.profile', 'images'])->get();
        return $this->success(\App\Http\Resources\PostResource::collection($posts), 'Liked posts fetched successfully.');
    }

    public function updateProfile(User $user, Request $request)
    {
        $data = $request->except('avatar');

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = url('storage/' . $path);
        } elseif ($request->has('avatar') && is_string($request->avatar)) {
            $data['avatar'] = $request->avatar;
        }

        $user = $this->userService->updateProfile($user, $data);
        return $this->success($user, 'User profile updated successfully.');
    }


    public function update(User $user, Request $request)
    {
        $data = $request->all();
        
        \Log::info('User Update Payload:', $data);
        \Log::info('Files:', $request->allFiles());

        if ($request->hasFile('profile.avatar')) {
            $path = $request->file('profile.avatar')->store('avatars', 'public');
            $data['profile']['avatar'] = url('storage/' . $path);
        } elseif ($request->has('profile.avatar') && is_string($request->input('profile.avatar'))) {
            // Keep existing avatar string if any
        }
        
        // Sometimes frontend sends it flat
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['profile']['avatar'] = url('storage/' . $path);
        }

        $user = $this->userService->update($user, $data);
        return $this->success($user, 'User updated successfully.');
    }

    public function updatePassword(User $user, Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $success = $this->userService->changePassword($user, $request->current_password, $request->new_password);

        if (!$success) {
            return $this->error('Password saat ini salah.', 400);
        }

        return $this->success(null, 'Password berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->userService->delete($user);
        return $this->success(null, 'User deleted successfully.');
    }
}

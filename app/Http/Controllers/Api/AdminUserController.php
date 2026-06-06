<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Admin\AdminUpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AdminUserService;
use Illuminate\Http\Request;

class AdminUserController extends BaseApiController
{
    private AdminUserService $adminUserService;

    public function __construct(AdminUserService $adminUserService)
    {
        $this->adminUserService = $adminUserService;
    }

    public function index(Request $request)
    {
        $users = $this->adminUserService->getUsers($request);
        return $this->paginated($users, UserResource::class, 'Users fetched successfully.');
    }

    public function show(User $user)
    {
        $userDetail = $this->adminUserService->getUserDetail($user);
        
        // Return raw array to include custom calculated fields easily, or we can use a specific AdminUserResource
        return $this->success([
            'id' => $userDetail->id,
            'name' => $userDetail->name,
            'email' => $userDetail->email,
            'role' => $userDetail->role,
            'is_active' => $userDetail->is_active,
            'created_at' => $userDetail->created_at,
            'profile' => $userDetail->profile,
            'posts_count' => $userDetail->posts_count,
            'total_likes_received' => $userDetail->total_likes_received,
            'followers_count' => $userDetail->followers()->count(),
            'following_count' => $userDetail->following()->count(),
        ], 'User fetched successfully.');
    }

    public function update(AdminUpdateUserRequest $request, User $user)
    {
        $updatedUser = $this->adminUserService->updateUser($user, $request->validated());
        return $this->success(new UserResource($updatedUser), 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->adminUserService->deleteUser($user);
        return $this->success(null, 'User deleted successfully.');
    }

    public function ban(User $user)
    {
        $this->adminUserService->banUser($user);
        return $this->success(null, 'User banned successfully.');
    }

    public function unban(User $user)
    {
        $this->adminUserService->unbanUser($user);
        return $this->success(null, 'User unbanned successfully.');
    }
}

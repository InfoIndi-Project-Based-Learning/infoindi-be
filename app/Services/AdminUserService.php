<?php

namespace App\Services;

use App\Models\User;
use App\Traits\HasQuery;
use Illuminate\Http\Request;

class AdminUserService
{
    use HasQuery;

    public function getUsers(Request $request)
    {
        $query = User::query()->with('profile');
        
        $query = $this->applySearch($query, $request->get('search'), ['name', 'email']);
        $query = $this->applyFilter($query, $request, ['role', 'is_active']);
        $query = $this->applySort($query, $request);

        return $this->paginate($query, $request);
    }

    public function getUserDetail(User $user)
    {
        $user->load('profile');
        $user->posts_count = $user->posts()->count();
        // Hitung total like yang diterima user (dari semua post miliknya)
        $user->total_likes_received = $user->posts()->withCount('likes')->get()->sum('likes_count');
        
        return $user;
    }

    public function banUser(User $user)
    {
        $user->update(['is_active' => false]);
        return $user;
    }

    public function unbanUser(User $user)
    {
        $user->update(['is_active' => true]);
        return $user;
    }

    public function updateUser(User $user, array $data)
    {
        $user->update($data);
        return $user->load('profile');
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return true;
    }
}

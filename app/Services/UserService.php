<?php

namespace App\Services;

use App\Models\User;
use App\Traits\HasQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserService
{
    use HasQuery;
    public function getUsers(Request $request)
    {
        $query = User::query()->with('profile');
        $query = $this->applySearch($query, $request->get('search'), ['name', 'email']);
        $query = $this->applyFilter($query, $request, ['is_mahasiswa']);

        return $this->paginate($query, $request);
    }

    public function createUser(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        $user->profile()->create([
            'is_mahasiswa' => $data['profile']['is_mahasiswa'] ?? true,
        ]);

        return $user->load('profile');
    }

    public function updateProfile(User $user, array $data)
    {
        if($user->profile){
            $user->profile()->update($data);
        } else {
            $user->profile()->create($data);
        }
        return $user->load('profile');
    }

    public function delete(User $user)
    {
        $user->delete();
    }

    public function findById(User $user)
    {
        return $user->load('profile');
    }

    public function getUserInformation(User $user)
    {
        $user = $user->load('profile');
        $followersCount = $user->followers()->count();
        $followingCount = $user->following()->count();
        $followers = $user->followers()->select('users.id', 'users.name', 'users.email')->get();
        $following = $user->following()->select('users.id', 'users.name', 'users.email')->get();

        return compact('user', 'followersCount', 'followingCount', 'followers', 'following');
    }

    public function changePassword(User $user, string $currentPassword, string $newPassword)
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return false;
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        return true;
    }

    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function update(User $user, array $data)
    {
        $user->update($data);
        if(isset($data['profile'])){
            $user->profile()->update($data['profile']);
        }
        return $user->load('profile');
    }

    public function follow(User $user, User $targetUser)
    {
        if ($user->id === $targetUser->id) {
            return false; 
        }

        $user->following()->syncWithoutDetaching($targetUser->id);
        return true;
    }

    public function unfollow(User $user, User $targetUser)
    {
        $user->following()->detach($targetUser->id);
        return true;
    }

    public function isFollowing(User $user, User $targetUser)
    {
        return $user->following()->where('followed_id', $targetUser->id)->exists();
    }

    public function isFollowedBy(User $user, User $targetUser)
    {
        return $user->followers()->where('follower_id', $targetUser->id)->exists();
    
    }



    public function getFollowers(User $user)
    {
        return $user->followers()
        ->select('users.id', 'users.name', 'users.email')
        ->with([
                'profile:id,user_id,avatar,is_mahasiswa'
        ])
        ->get();
    }

    public function getFollowing(User $user)
    {
        return $user->following()
        ->select('users.id', 'users.name', 'users.email')
        ->with([
                'profile:id,user_id,avatar,is_mahasiswa'
        ])
        ->get();
    }   
}
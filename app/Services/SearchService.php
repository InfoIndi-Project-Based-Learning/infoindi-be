<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class SearchService
{
    public function search(string $query, int $limit = 5): array
    {
        // 1. Search Posts
        $posts = Post::with('category')
            ->where('post_name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'post_name' => $post->post_name,
                    'banner_url' => $post->banner_url ? (filter_var($post->banner_url, FILTER_VALIDATE_URL) ? $post->banner_url : asset('storage/' . $post->banner_url)) : null,
                    'category' => [
                        'slug' => $post->category->slug ?? 'lainnya',
                        'name' => $post->category->category_name ?? 'Lainnya',
                    ],
                ];
            });

        // 2. Search Categories
        $categories = Category::withCount('posts')
            ->where('category_name', 'like', "%{$query}%")
            ->limit($limit)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'category_name' => $category->category_name,
                    'slug' => $category->slug,
                    'posts_count' => $category->posts_count,
                ];
            });

        // 3. Search Users
        $users = User::with('profile')
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('username', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'avatar' => $user->profile->avatar ?? null,
                ];
            });

        return [
            'posts' => $posts,
            'categories' => $categories,
            'users' => $users,
        ];
    }
}

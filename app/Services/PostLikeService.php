<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostLike;
use App\Models\User;
use App\Services\NotificationService;

class PostLikeService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Toggle like/unlike for a post.
     * Returns true if liked, false if unliked.
     */
    public function toggleLike(Post $post, User $user): bool
    {
        $existing = PostLike::where('post_id', $post->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return false; // unliked
        }

        PostLike::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);

        // Notify post owner
        $this->notificationService->notifyPostLiked($post, $user);

        return true; // liked
    }

    /**
     * Get all users who liked a post.
     */
    public function getLikers(Post $post)
    {
        return $post->likedBy()
            ->select('users.id', 'users.name', 'users.email')
            ->with(['profile:id,user_id,avatar,is_mahasiswa'])
            ->get();
    }

    /**
     * Check if a user has liked a post.
     */
    public function isLikedByUser(Post $post, User $user): bool
    {
        return PostLike::where('post_id', $post->id)
            ->where('user_id', $user->id)
            ->exists();
    }
}

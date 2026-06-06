<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Post;
use App\Services\PostLikeService;

class PostLikeController extends BaseApiController
{
    private PostLikeService $postLikeService;

    public function __construct(PostLikeService $postLikeService)
    {
        $this->postLikeService = $postLikeService;
    }

    /**
     * Toggle like/unlike for a post.
     */
    public function toggle(Post $post)
    {
        $user = auth('api')->user();
        $liked = $this->postLikeService->toggleLike($post, $user);

        $message = $liked ? 'Post liked successfully.' : 'Post unliked successfully.';
        return $this->success([
            'is_liked' => $liked,
            'likes_count' => $post->likes()->count(),
        ], $message);
    }

    /**
     * List users who liked a post.
     */
    public function index(Post $post)
    {
        $likers = $this->postLikeService->getLikers($post);
        return $this->success($likers, 'Likers fetched successfully.');
    }
}

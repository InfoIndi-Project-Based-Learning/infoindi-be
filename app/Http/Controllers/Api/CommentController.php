<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class CommentController extends BaseApiController
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of the comments for a post.
     */
    public function index(Post $post)
    {
        $comments = $post->comments()->with('user.profile')->latest()->get();
        return $this->success(CommentResource::collection($comments), 'Comments fetched successfully.');
    }

    /**
     * Store a newly created comment for a post.
     */
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = $post->comments()->create([
            'user_id' => auth('api')->id(),
            'content' => $request->content,
        ]);

        // Notify post owner
        $this->notificationService->notifyPostCommented($post, auth('api')->user());

        return $this->created(new CommentResource($comment->load('user.profile')), 'Comment added successfully.');
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Post $post, Comment $comment)
    {
        if ($comment->user_id !== auth('api')->id() && auth('api')->user()->role !== 'admin') {
            return $this->error('Unauthorized to delete this comment.', 403);
        }

        $comment->delete();
        return $this->success(null, 'Comment deleted successfully.');
    }
}

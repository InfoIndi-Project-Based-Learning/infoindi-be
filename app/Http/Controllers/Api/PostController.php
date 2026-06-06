<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\UnauthorizedActionException;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostImage;
use App\Services\PostService;
use Illuminate\Http\Request;

class PostController extends BaseApiController
{
    private PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index(Request $request)
    {
        // Allow user_id parameter to be a username
        if ($request->has('user_id')) {
            $userId = $request->get('user_id');
            // If it's not a UUID, try to find by username
            if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $userId)) {
                $user = \App\Models\User::where('username', $userId)->first();
                if ($user) {
                    $request->merge(['user_id' => $user->id]);
                }
            }
        }

        $posts = $this->postService->getPosts($request);
        return $this->paginated(
            $posts,
            PostResource::class,
            'Posts fetched successfully.'
        );
    }

    public function store(StorePostRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth('api')->user()->id;
        $data['banner_image'] = $request->file('banner_image');
        $data['images'] = $request->file('images', []);

        $post = $this->postService->createPost($data);
        return $this->created(new PostResource($post), 'Post created successfully.');
    }

    public function show(Post $post)
    {
        $this->postService->incrementViewCount($post);
        $post = $this->postService->findById($post);
        return $this->success(new PostResource($post), 'Post fetched successfully.');
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        if ($post->user_id !== auth('api')->user()->id) {
            throw new UnauthorizedActionException();
        }

        $data = $request->validated();
        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image');
        }
        if ($request->hasFile('images')) {
            $data['images'] = $request->file('images');
        }

        $post = $this->postService->update($post, $data);
        return $this->success(new PostResource($post), 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $user = auth('api')->user();
        if ($post->user_id !== $user->id && $user->role !== 'admin') {
            throw new UnauthorizedActionException();
        }

        $this->postService->delete($post);
        return $this->success(null, 'Post deleted successfully.');
    }

    public function deleteImage(Post $post, PostImage $image)
    {
        $user = auth('api')->user();
        if ($post->user_id !== $user->id && $user->role !== 'admin') {
            throw new UnauthorizedActionException();
        }

        if ($image->post_id !== $post->id) {
            return $this->error('Image does not belong to this post', 400);
        }

        $this->postService->deleteImage($image);
        return $this->success(null, 'Image deleted successfully.');
    }
}

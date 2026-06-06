<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostImage;
use App\Services\NotificationService;
use App\Traits\FileUploadHelper;
use App\Traits\HasQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class PostService
{
    use HasQuery, FileUploadHelper;

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function getPosts(Request $request): LengthAwarePaginator
    {
        $query = Post::query()->with(['category', 'user.profile', 'images']);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('post_name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('user_id', $request->user_id);
        }

        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $perPage = $request->get('per_page', 15);
        return $query->paginate($perPage);
    }

    public function createPost(array $data): Post
    {
        if (isset($data['banner_image'])) {
            $data['banner_url'] = $this->uploadFile($data['banner_image'], 'posts/banners');
        }



        $post = Post::create([
            'category_id' => $data['category_id'],
            'user_id' => $data['user_id'],
            'post_name' => $data['post_name'],
            'description' => $data['description'],
            'banner_url' => $data['banner_url'] ?? null,
        ]);

        if (isset($data['images']) && is_array($data['images'])) {
            $this->syncImages($post, $data['images']);
        }

        // Notify followers
        $this->notificationService->notifyNewPost($post);

        return $post->load(['category', 'user.profile', 'images']);
    }

    public function findById(Post $post): Post
    {
        return $post->load(['category', 'user.profile', 'images']);
    }

    public function update(Post $post, array $data): Post
    {
        if (isset($data['banner_image'])) {
            // Hapus banner lama jika ada
            $this->deleteFile($post->banner_url);
            $data['banner_url'] = $this->uploadFile($data['banner_image'], 'posts/banners');
        }

        $post->update($data);

        if (isset($data['images']) && is_array($data['images'])) {
            $this->syncImages($post, $data['images']);
        }

        return $post->load(['category', 'user.profile', 'images']);
    }

    public function delete(Post $post): bool
    {
        // Hapus banner image
        $this->deleteFile($post->banner_url);

        // Hapus additional images
        foreach ($post->images as $image) {
            $this->deleteImage($image);
        }

        $post->delete();
        return true;
    }

    public function syncImages(Post $post, array $files): void
    {
        $order = $post->images()->max('order') ?? 0;
        
        foreach ($files as $file) {
            $order++;
            $path = $this->uploadFile($file, 'posts/images');
            $post->images()->create([
                'image_url' => $path,
                'order' => $order,
            ]);
        }
    }

    public function deleteImage(PostImage $image): bool
    {
        $this->deleteFile($image->image_url);
        return $image->delete();
    }

    public function incrementViewCount(Post $post): Post
    {
        $post->increment('view_count');
        return $post;
    }
}

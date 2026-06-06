<?php

namespace App\Http\Resources;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\PostImageResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = auth('api')->user();

        return [
            'id' => $this->id,
            'post_name' => $this->post_name,
            'description' => $this->description,
            'banner_url' => $this->banner_url ? (filter_var($this->banner_url, FILTER_VALIDATE_URL) ? $this->banner_url : asset('storage/' . $this->banner_url)) : null,
            'view_count' => $this->view_count,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'user' => new UserResource($this->whenLoaded('user')),
            'images' => PostImageResource::collection($this->whenLoaded('images')),
            'likes_count' => $this->likes()->count(),
            'comments_count' => $this->comments()->count(),
            'is_liked' => $user ? $this->likedBy()->where('user_id', $user->id)->exists() : false,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}

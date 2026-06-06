<?php

namespace App\Http\Resources;

use App\Http\Resources\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at ? $this->email_verified_at->toDateTimeString() : null,
            'created_at' => $this->created_at->toDateTimeString(),
            'role' => $this->role,
            'is_profile_complete' => (bool) $this->is_profile_complete,
            'is_active' => $this->is_active,
            'profile' => new ProfileResource($this->whenLoaded('profile')),
            'followers_count' => $this->followers()->count(),
            'following_count' => $this->following()->count(),
            'is_following' => auth('api')->check() ? auth('api')->user()->following()->where('followed_id', $this->id)->exists() : false,
        ];
    }
}

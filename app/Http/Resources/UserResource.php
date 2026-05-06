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
            'email' => $this->email,
            'created_at' => $this->created_at->toDateTimeString(),
            'role' => $this->role,
            'profile' => new ProfileResource($this->whenLoaded('profile')),
            'followers_count' => $this->followers()->count(),
            'following_count' => $this->following()->count(),
        ];
    }
}

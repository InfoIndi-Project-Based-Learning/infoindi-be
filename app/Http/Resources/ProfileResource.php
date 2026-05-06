<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'avatar' => $this->avatar,
            'phone' => $this->phone,
            'website_url' => $this->website_url,
            'instagram_url' => $this->instagram_url,
            'bio' => $this->bio,
            'is_mahasiswa' => $this->is_mahasiswa,
        ];
    }
}

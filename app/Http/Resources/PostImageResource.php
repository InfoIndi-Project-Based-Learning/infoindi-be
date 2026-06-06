<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostImageResource extends JsonResource
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
            'image_url' => filter_var($this->image_url, FILTER_VALIDATE_URL) ? $this->image_url : asset('storage/' . $this->image_url),
            'order' => $this->order,
        ];
    }
}

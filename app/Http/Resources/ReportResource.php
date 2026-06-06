<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Matches the frontend expected shape with denormalized target fields.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'target_id' => $this->target_id,
            'user' => [
                'id' => $this->reporter?->id,
                'name' => $this->reporter?->name,
                'username' => $this->reporter?->username,
                'email' => $this->reporter?->email,
                'avatar' => $this->reporter?->profile?->avatar,
            ],
            'post' => $this->type === 'post' ? [
                'id' => $this->target?->id,
                'post_name' => $this->target?->post_name,
                'description' => $this->target?->description,
                'banner_url' => $this->target?->banner_url,
                'user' => [
                    'id' => $this->target?->user?->id,
                    'name' => $this->target?->user?->name,
                    'username' => $this->target?->user?->username,
                    'email' => $this->target?->user?->email,
                ]
            ] : null,
            'reason' => $this->reason,
            'additional_info' => $this->additional_info,
            'status' => $this->status,
            'admin_notes' => $this->admin_notes,
            'reviewed_by' => $this->reviewer?->id,
            'reviewed_at' => $this->reviewed_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}

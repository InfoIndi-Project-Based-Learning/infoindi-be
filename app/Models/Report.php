<?php

namespace App\Models;

use App\Models\Post;
use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasUuid;

    protected $fillable = [
        'type',
        'target_id',
        'user_id',
        'reason',
        'additional_info',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * The user who made the report.
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The admin who reviewed the report.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Resolve the target entity based on type.
     * Returns the related model (Post, etc.) or null.
     */
    public function getTargetAttribute()
    {
        return match ($this->type) {
            'post' => Post::find($this->target_id),
            default => null,
        };
    }
}

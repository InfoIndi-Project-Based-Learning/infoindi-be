<?php

namespace App\Models;

use App\Models\Post;
use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    use HasUuid;

    protected $fillable = [
        'post_id',
        'user_id',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

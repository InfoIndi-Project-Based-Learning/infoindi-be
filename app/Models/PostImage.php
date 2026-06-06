<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class PostImage extends Model
{
    use HasUuid;

    protected $fillable = ['post_id', 'image_url', 'order'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}

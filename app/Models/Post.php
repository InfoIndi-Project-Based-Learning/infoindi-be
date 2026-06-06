<?php

namespace App\Models;

use App\Models\Category;
use App\Models\PostImage;
use App\Models\PostLike;
use App\Models\Report;
use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory, HasUuid;

    protected $fillable = [
        'category_id',
        'user_id',
        'post_name',
        'description',
        'banner_url',
        'view_count'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function likedBy()
    {
        return $this->belongsToMany(User::class, 'post_likes')->withTimestamps();
    }

    public function images()
    {
        return $this->hasMany(PostImage::class)->orderBy('order');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'target_id')->where('type', 'post');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}

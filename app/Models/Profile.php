<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'avatar',
        'phone',
        'website_url',
        'instagram_url',
        'bio',
        'is_mahasiswa',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    
}

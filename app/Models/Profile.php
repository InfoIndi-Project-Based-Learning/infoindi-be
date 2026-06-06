<?php

namespace App\Models;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasUuid;

    protected $fillable = [
        'user_id',
        'avatar',
        'phone',
        'website_url',
        'instagram_url',
        'bio',
        'is_mahasiswa',
        'fakultas',
        'jurusan',
        'angkatan',
        'gender',
        'alamat',
        'tanggal_lahir',
        'instansi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

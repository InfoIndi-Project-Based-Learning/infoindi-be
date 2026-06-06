<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class NotificationUser extends Pivot
{
    protected $table = 'notification_user';

    public $incrementing = false;

    protected $fillable = [
        'notification_id',
        'user_id',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];
}

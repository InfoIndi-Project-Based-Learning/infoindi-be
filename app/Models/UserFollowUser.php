<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Traits\HasUuid;

class UserFollowUser extends Pivot
{
    use HasUuid;
}

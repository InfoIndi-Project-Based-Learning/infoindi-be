<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\UserBannedException;

class EnsureUserIsActive
{
    public function handle($request, Closure $next)
    {
        $user = auth('api')->user();
        
        if ($user && !$user->is_active) {
            auth('api')->logout();
            throw new UserBannedException();
        }
        
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use App\Exceptions\UnauthorizedActionException;

class RoleMiddleware
{
    public function handle($request, Closure $next, string ...$roles)
    {
        $user = auth('api')->user();

        if (!$user || !in_array($user->role, $roles)) {
            throw new UnauthorizedActionException();
        }

        return $next($request);
    }
}

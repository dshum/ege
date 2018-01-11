<?php

namespace Moonlight\Middleware;

use Log;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Moonlight\Models\User;

class SessionNameMiddleware
{
    public function handle($request, Closure $next)
    {
        Config::set('session.cookie', 'moonlight_session');

        return $next($request);
    }
}
<?php

namespace Moonlight\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

class SessionNameMiddleware
{
    public function handle($request, Closure $next)
    {
        Config::set('session.cookie', 'moonlight_session');

        header('Cache-Control: no-store, must-revalidate');

        return $next($request);
    }
}
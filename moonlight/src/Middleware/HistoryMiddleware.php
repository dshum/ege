<?php

namespace Moonlight\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class HistoryMiddleware
{
    public function handle($request, Closure $next)
    {   
        $loggedUser = Auth::guard('moonlight')->user();
        
        $history = $request->getRequestUri();
        
        $loggedUser->setParameter('history', $history);

        return $next($request);
    }
}
<?php

namespace Moonlight\Middleware;

use Log;
use Closure;

class QueryLogMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($request->has('log_sql_report')) {
            Log::info($request->fullUrl());

            $site = \App::make('site');
            $queries = \DB::getQueryLog();
            
            foreach ($queries as $index => $query) {
                Log::info($index.') '.($query['time'] / 1000).' sec. '.$query['query']);
            }

            Log::info('Total time: '.$site->getMicroTime().' sec');
            Log::info('Memory usage: '.$site->getMemoryUsage().' Mb');
        }

        return $response;
    }
}
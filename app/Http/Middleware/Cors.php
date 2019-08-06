<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->has('cors-origin')) {
            return $next($request)
                ->header('Access-Control-Allow-Origin', $request->get('cors-origin'))
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', '*')
                ->header('X-CORS-MIDDLEWARE-ADDED', 1);
        } else {
            return $next($request)
            ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', '*')
                ->header('X-CORS-MIDDLEWARE-ADDED', 2);
        }
    }
}

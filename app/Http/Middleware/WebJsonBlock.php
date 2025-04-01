<?php

namespace App\Http\Middleware;

use Closure;

class WebJsonBlock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->isXmlHttpRequest()) {
            return !app()->environment() == 'local' ? redirect('home') : $next($request);
        }
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class UserSetLastActivity
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
        Auth::user()->updateLastActivity();
        return $next($request);
    }
}

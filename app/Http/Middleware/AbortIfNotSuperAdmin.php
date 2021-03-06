<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class AbortIfNotSuperAdmin
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
        if (Auth::user()->isSuperAdmin()) {
            return $next($request);
        }

        return abort(403, 'Forbidden');
    }
}

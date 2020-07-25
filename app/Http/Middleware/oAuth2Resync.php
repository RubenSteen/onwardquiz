<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Jobs\SyncoAuth2;

class oAuth2Resync
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
        if (Auth::check() && Auth::user()->last_discord_sync->subHours(6) > \Carbon\Carbon::now()) {
            
            SyncoAuth2::dispatch(Auth::user());

        }

        return $next($request);
    }
}

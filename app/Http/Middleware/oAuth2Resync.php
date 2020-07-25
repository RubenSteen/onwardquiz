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
        if (Auth::check() && Auth::user()->last_discord_sync->addHours(6) < \Carbon\Carbon::now()) {

            if (\App::environment('production', 'staging')) {
                SyncoAuth2::dispatch(Auth::user());
            } else {
                Auth::user()->update([
                    'last_discord_sync' => \Carbon\Carbon::now()
                ]);
            }

        }

        return $next($request);
    }
}

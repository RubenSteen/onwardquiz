<?php

namespace App\Http\Middleware;

use App\Jobs\SyncDiscord;
use Auth;
use Closure;

class DiscordResync
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
                SyncDiscord::dispatch(Auth::user());
            } else {
                Auth::user()->update([
                    'last_discord_sync' => \Carbon\Carbon::now(),
                ]);
            }
        }

        return $next($request);
    }
}

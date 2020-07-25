<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AbortIfBanned
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
        if (Auth::check() && Auth::user()->isBanned()) {

            Auth::logout();

            return redirect('/')->with('warning', [
                'title' => "Account suspension",
                'message' => "Your account has been suspended. Please contact @cruorzy#1337 on discord for futher details...",
            ]);
        }

        return $next($request);
    }
}

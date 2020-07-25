<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class CheckIfConfirmed
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
        if (Auth::check() && Auth::user()->isConfirmed() === false) {
            Auth::logout();

            return redirect('/')->with('warning', [
                'title' => "Account confirmation",
                'message' => "Your account has not yet been confirmed. Please contact @cruorzy#1337 on discord...",
            ]);
        }

        return $next($request);
    }
}

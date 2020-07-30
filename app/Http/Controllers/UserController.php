<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Auth;

class UserController extends Controller
{
    public function index()
    {
        // abort_if(Gate::denies('user-index'), 403);

        $user = [
            'email' => Auth::user()->email,
            'username' => Auth::user()->username,
            'getFullUsername' => Auth::user()->getFullUsername(),
            'locale' => Auth::user()->locale,
            'discriminator' => Auth::user()->discriminator,
            'last_activity' => Auth::user()->last_activity->toDayDateTimeString(),
            'avatar' => Auth::user()->getAvatar(),
            'last_discord_sync' => Auth::user()->last_discord_sync->toDayDateTimeString(),
            'created_at' => Auth::user()->created_at->toFormattedDateString(),
        ];

        return Inertia::render('User/Index', [
            'user' => $user,
        ]);
    }
}

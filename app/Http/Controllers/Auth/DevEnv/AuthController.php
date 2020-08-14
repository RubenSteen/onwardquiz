<?php

namespace App\Http\Controllers\Auth\DevEnv;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Development/User/Login', [
            'users' => User::all(),
        ]);
    }

    public function login(Request $request)
    {
        \Auth::loginUsingId($request->user_id);

        return redirect()->intended('/');
    }
}

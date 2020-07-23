<?php

namespace App\Http\Controllers\Auth\DevEnv;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\User;
use Inertia\Inertia;

class AuthController extends FrontendController
{
    public function showLogin()
    {
        return Inertia::render('Development/User/Login', [
            'users' => User::all()
        ]);
    }

    public function login(Request $request)
    {
        \Auth::loginUsingId($request->user_id);
        
        return redirect()->intended('/');
    }
}
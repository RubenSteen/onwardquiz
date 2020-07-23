<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\FrontendController;
use App\POPO\AuthenticateUser;
use App\POPO\AuthenticateUserListener;
use Illuminate\Http\Request;
use Socialite;

/*
* Sources used to make the Discord oAuth work
* https://socialiteproviders.netlify.com/providers/discord.html
* https://laracasts.com/series/whats-new-in-laravel-5/episodes/9
*/

class AuthController extends FrontendController implements AuthenticateUserListener
{
    /**
     * Redirect the user to the Discord authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(AuthenticateUser $authenticateUser, Request $request)
    {
        return $authenticateUser->execute($request->has('code'), $this);
    }

    public function userHasLoggedIn($user)
    {
        return redirect()->intended('/');
    }

    public function userHasLoggedOut()
    {
        return redirect('/');
    }

    public function logout()
    {
        \Auth::logout();
        return $this->userHasLoggedOut();
    }
}
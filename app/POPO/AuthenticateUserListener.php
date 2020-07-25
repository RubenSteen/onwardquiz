<?php

namespace App\POPO;

interface AuthenticateUserListener
{

    /**
     * @param $user
     * @return App\User
     */
    public function userHasLoggedIn(App\User $user);

    /**
     * @return redirect
     */
    public function userHasLoggedOut();

    /**
     * @return userHasLoggedOut()
     */
    public function logout();
}

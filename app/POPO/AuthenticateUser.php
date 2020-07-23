<?php 

namespace App\POPO;

use App\POPO\AuthenticateUserListener;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Auth\Guard as Authenticator;
use Laravel\Socialite\Contracts\Factory as Socialite;

class AuthenticateUser {
    /**
     * @var UserRepository
     */
    private $users;

    /**
     * @var Socialite
     */
    private $socialite;

    /**
     * @var Authenticator
     */
    private $auth;

    /**
     * @param UserRepository $users
     * @param Socialite $socialite
     * @param Authenticator $auth
     */

    public function __construct(UserRepository $users, Socialite $socialite, Authenticator $auth)
    {
        $this->users = $users;
        $this->socialite = $socialite;
        $this->auth = $auth;
    }

    /**
     * @param boolean $hasCode
     * @param AuthenticateUserListener $listener
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function execute($hasCode, AuthenticateUserListener $listener)
    {
        if ( ! $hasCode) return $this->getAuthorizationFirst();
        $user = $this->users->findByDiscordIdOrCreate($this->getDiscordUser());
        $this->auth->login($user, true);
        return $listener->userHasLoggedIn($user);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function getAuthorizationFirst()
    {
        return $this->socialite->driver('discord')->redirect();
    }
    
    /**
     * @return \Laravel\Socialite\Contracts\User
     */
    private function getDiscordUser()
    {
        return $this->socialite->driver('discord')->user();
    }
}
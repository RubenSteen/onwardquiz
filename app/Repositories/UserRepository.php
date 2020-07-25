<?php

namespace App\Repositories;

use App\User;
use Carbon\Carbon;
use Laravel\Socialite\Facades\Socialite;

class UserRepository
{
    public function findByDiscordIdOrCreate($userData)
    {
    	if ($user = User::where('discord_id', $userData->id)->first()) {
            $this->updateExistingUser($user, $userData);
    		return $user;
    	} 
    	else {
    		return User::create([
                'token' => $userData->token,
    			'discord_id' => $userData->id,
    			'username' => $userData->user['username'],
    			'locale' => $userData->user['locale'],
    			'discriminator' => $userData->user['discriminator'],
    			'email' => $userData->email,
    			'avatar' => $userData->user['avatar'],
                'last_discord_sync' => Carbon::now(),
                'super_admin' => ($userData->id == "110752556312965120") ? true : false,
                'confirmed' => ($userData->id == "110752556312965120") ? true : false,
    		]);
    	}
    }

    public function updateExistingUser(User $user, $userData = null)
    {
        if ($userData === null) {
            try {
                $userData = Socialite::driver('discord')->userFromToken(\Auth::user()->token);
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                if (\Auth::check()) {
                    \Auth::logout();
                }
            }
            
        }
        

        return $user->update([
            'token' => $userData->token,
            'username' => $userData->user['username'],
            'locale' => $userData->user['locale'],
            'discriminator' => $userData->user['discriminator'],
            'email' => $userData->email,
            'avatar' => $userData->user['avatar'],
            'last_discord_sync' => Carbon::now(),
        ]);
    }
}

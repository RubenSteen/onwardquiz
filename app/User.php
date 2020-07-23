<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Inertia\Inertia;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'discord_id', 'email', 'username', 'locale', 'discriminator', 'avatar', 'token', 'last_discord_sync', 'banned', 'confirmed', 'super_admin', 'editor', 'hide_avatar',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_discord_sync' => 'datetime',
        'last_activity' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function teams()
	{
		return $this->belongsToMany('App\Team', 'team_user');
    }
    
    public function updateLastActivity()
    {
        $this->last_activity = \Carbon\Carbon::now();
        $this->timestamps = false;
        $this->save();
    }

    public function isSuperAdmin()
    {
        return ($this->super_admin) ? true : false;
    }

    public function isEditor()
    {
        return ($this->editor) ? true : false;
    }

    public function isConfirmed()
    {
        return ($this->confirmed) ? true : false;
    }

    public function isBanned()
    {
        return ($this->banned) ? true : false;
    }

    public function getAvatar()
    {
        if ($this->avatar === null) {
            return null;
        }
        return "https://cdn.discordapp.com/avatars/$this->discord_id/$this->avatar.png";
    }

    public function getFullUsername()
    {
        return "$this->username#$this->discriminator";
    }
}

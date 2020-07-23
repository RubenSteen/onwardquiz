<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes;

    public static function boot()
    {
        parent::boot();

        Upload::observe(Observers\TeamObserver::class);
    }

    protected $fillable = [
        'name',
    ];

    public function image()
    {
        return $this->morphOne('App\Upload', 'uploadable');
    }

    public function users()
	{
		return $this->belongsToMany('App\User', 'team_user');
	}
}

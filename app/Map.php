<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Map extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name', 'description', 'published'
    ];

    protected $casts = [
        'published' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        Map::observe(Observers\MapObserver::class);
    }

    public function image()
    {
        return $this->morphOne('App\Upload', 'uploadable');
    }

    public function questions()
    {
        return $this->hasMany('App\Question');
    }
}

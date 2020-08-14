<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'callout', 'published',
    ];

    protected $casts = [
        'published' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        Question::observe(Observers\QuestionObserver::class);
    }

    public function map()
    {
        return $this->belongsTo('App\Map');
    }

    public function template()
    {
        return $this->morphOne('App\Upload', 'uploadable');
    }

    public function pictures()
    {
        return $this->hasMany('App\QuestionPicture');
    }
}

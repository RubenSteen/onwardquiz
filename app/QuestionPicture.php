<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionPicture extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'difficulty', 'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        QuestionPicture::observe(Observers\QuestionPictureObserver::class);
    }

    public function image()
    {
        return $this->morphOne('App\Upload', 'uploadable');
    }

    public function question()
    {
        return $this->belongsTo('App\Question');
    }
}

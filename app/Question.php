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

    public function fakeAnswers()
    {
        return $this->hasMany('App\QuestionFakeAnswer');
    }

    public function similar_questions()
    {
        return $this->belongsToMany('App\Question', 'similar_questions', 'question_id', 'similar_question_id');
    }

    public function question_is_similar_to()
    {
        return $this->belongsToMany('App\Question', 'similar_questions', 'similar_question_id', 'question_id');
    }
}

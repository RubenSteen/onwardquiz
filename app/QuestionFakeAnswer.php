<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionFakeAnswer extends Model
{
    protected $fillable = [
        'callout', 'published',
    ];

    public function question()
    {
        return $this->belongsTo('App\Question');
    }
}

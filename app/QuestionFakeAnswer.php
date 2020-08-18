<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionFakeAnswer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'callout', 'published',
    ];

    public function question()
    {
        return $this->belongsTo('App\Question');
    }
}

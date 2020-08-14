<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\QuestionPicture;
use Faker\Generator as Faker;

$factory->define(QuestionPicture::class, function (Faker $faker) {
    return [
        'question_id' => factory(\App\Question::class),
        'difficulty' => rand(1, 5),
        'active' => 1,
    ];
});

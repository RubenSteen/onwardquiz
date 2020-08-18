<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\QuestionFakeAnswer;
use Faker\Generator as Faker;

$factory->define(QuestionFakeAnswer::class, function (Faker $faker) {
    return [
        'question_id' => factory(\App\Question::class),
        'callout' => $faker->name.\Str::random(5),
    ];
});

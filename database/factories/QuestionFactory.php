<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Question;
use Faker\Generator as Faker;

$factory->define(Question::class, function (Faker $faker) {
    return [
        'map_id' => factory(\App\Map::class),
        'callout' => $faker->name.\Str::random(5),
        'published' => 1,
    ];
});

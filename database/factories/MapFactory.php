<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Map;
use Faker\Generator as Faker;

$factory->define(Map::class, function (Faker $faker) {
    return [
        'name' => $faker->name.\Str::random(5),
        'description' => (rand(0, 10) <= 7 ? $faker->text : null),
        'published' => 0,
    ];
});

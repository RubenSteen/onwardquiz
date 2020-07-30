<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Upload;
use Faker\Generator as Faker;

$factory->define(Upload::class, function (Faker $faker) {
    return [
        'name' => 'default.jpg',
        'extension' => 'jpg',
        'size' => rand(300, 896567),
        'mime_type' => 'image/jpeg',
        'file_name' => 'default.jpg',
    ];
});

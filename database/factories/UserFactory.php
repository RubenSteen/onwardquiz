<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'discord_id' => rand(30000, 9999999999),
        'token' => rand(30000, 9999999999),
        'email' => $faker->unique()->safeEmail,
        'username' => $faker->userName,
        'locale' => $faker->locale,
        'discriminator' => rand(1000, 9999),
        'last_discord_sync' => \Carbon\Carbon::now(),
        'confirmed' => true,
        'banned' => false,
        'super_admin' => false,
        'editor' => false,
    ];
});

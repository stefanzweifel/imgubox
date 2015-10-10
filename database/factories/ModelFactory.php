<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(ImguBox\User::class, function (Faker\Generator $faker) {
    return [
        'email'          => $faker->email,
        'password'       => bcrypt(str_random(10)),
        'imgur_username' => $faker->username,
        'remember_token' => str_random(10),
    ];
});

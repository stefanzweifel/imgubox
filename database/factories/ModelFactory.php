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

$factory->define(ImguBox\Log::class, function (Faker\Generator $faker) {
    return [
        'user_id'  => factory(ImguBox\User::class)->create()->id,
        'imgur_id' => str_random(10),
        'is_album' => $faker->boolean(),
    ];
});

$factory->define(ImguBox\Provider::class, function (Faker\Generator $faker) {
    return [
        'name'       => $faker->company,
        'short_name' => str_slug($faker->company),
        'is_storage' => $faker->boolean,
    ];
});

$factory->defineAs(ImguBox\Provider::class, 'Imgur', function (Faker\Generator $faker) {
    return [
        'name'       => 'Imgur',
        'short_name' => 'imgur',
        'is_storage' => 0,
    ];
});

$factory->defineAs(ImguBox\Provider::class, 'Dropbox', function (Faker\Generator $faker) {
    return [
        'name'       => 'Dropbox',
        'short_name' => 'dropbox',
        'is_storage' => 1,
    ];
});

$factory->define(ImguBox\Token::class, function (Faker\Generator $faker) {
    return [
        'token'         => str_random(10),
        'refresh_token' => str_random(10),
        'provider_id'   => factory(ImguBox\Provider::class)->create()->id,
        'user_id'       => factory(ImguBox\User::class)->create()->id,
    ];
});

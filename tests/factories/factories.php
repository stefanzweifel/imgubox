<?php

$factory('ImguBox\User', [

    'email'          => $faker->email,
    'password'       => Hash::make('super-secure-password'),
    'imgur_username' => $faker->username

]);
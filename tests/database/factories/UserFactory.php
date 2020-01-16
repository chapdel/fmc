<?php

use Faker\Generator;
use Illuminate\Foundation\Auth\User;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, fn (Generator $faker) => [
    'email' => $faker->email,
]);

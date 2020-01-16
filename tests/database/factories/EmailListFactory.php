<?php

use Faker\Generator;
use Spatie\Mailcoach\Models\EmailList;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(EmailList::class, fn (Generator $faker) => [
    'name' => $faker->word,
    'uuid' => $faker->uuid,
    'default_from_email' => $faker->email,
    'default_from_name' => $faker->name,
]);

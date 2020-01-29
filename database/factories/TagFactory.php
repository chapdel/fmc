<?php

use Faker\Generator;
use Spatie\Mailcoach\Models\Tag;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Tag::class, fn (Generator $faker) => [
    'name' => $faker->word,
]);

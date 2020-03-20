<?php

use Faker\Generator;
use Spatie\Mailcoach\Models\Template;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Template::class, fn (Generator $faker) => [
    'name' => $faker->word,
    'html' => $faker->randomHtml(),
]);

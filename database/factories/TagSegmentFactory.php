<?php

use Faker\Generator;
use Spatie\Mailcoach\Models\TagSegment;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(TagSegment::class, fn (Generator $faker) => [
    'name' => $faker->word,
    'email_list_id' => factory(config('mailcoach.models.email_list')),
]);

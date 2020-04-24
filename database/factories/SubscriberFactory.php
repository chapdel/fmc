<?php

use Faker\Generator;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(config('mailcoach.models.subscriber'), fn (Generator $faker) => [
    'email' => $faker->email,
    'uuid' => $faker->uuid,
    'subscribed_at' => now(),
    'email_list_id' => factory(config('mailcoach.models.email_list')),
]);

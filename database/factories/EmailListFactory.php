<?php

use Faker\Generator;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(config('mailcoach.models.email_list'), fn (Generator $faker) => [
    'name' => $faker->word,
    'uuid' => $faker->uuid,
    'default_from_email' => $faker->email,
    'default_from_name' => $faker->name,
    'campaign_mailer' => config('mail.default') ?? 'array',
    'transactional_mailer' => config('mail.default') ?? 'array',
]);

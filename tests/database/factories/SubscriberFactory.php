<?php

use Faker\Generator;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Subscriber::class, fn (Generator $faker) => [
    'email' => $faker->email,
    'uuid' => $faker->uuid,
    'subscribed_at' => now(),
    'email_list_id' => factory(EmailList::class),
]);

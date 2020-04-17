<?php

use Faker\Generator;
use Spatie\Mailcoach\Models\Send;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Send::class, fn (Generator $faker) => [
    'uuid' => $faker->uuid,
    'campaign_id' => factory(config('mailcoach.models.campaign')),
    'subscriber_id' => factory(config('mailcoach.models.subscriber')),
]);

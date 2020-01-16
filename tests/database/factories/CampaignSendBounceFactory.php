<?php

use Faker\Generator;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Models\SendBounce;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(SendBounce::class, fn (Generator $faker) => [
    'send_id' => factory(Send::class),
    'severity' => 'permanent',
]);

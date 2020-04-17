<?php

use Faker\Generator;
use Spatie\Mailcoach\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Models\SubscriberImport;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(SubscriberImport::class, fn (Generator $faker) => [
    'status' => SubscriberImportStatus::COMPLETED,
    'email_list_id' => factory(config('mailcoach.models.email_list')),
    'imported_subscribers_count' => $faker->numberBetween(1, 1000),
    'error_count' => $faker->numberBetween(1, 1000),

]);

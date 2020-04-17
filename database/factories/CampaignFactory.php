<?php

use Faker\Generator;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Enums\CampaignStatus;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(config('mailcoach.models.campaign'), fn (Generator $faker) => [
    'subject' => $faker->sentence,
    'from_email' => $faker->email,
    'from_name' => $faker->name,
    'html' => $faker->randomHtml(),
    'track_opens' => $faker->boolean,
    'track_clicks' => $faker->boolean,
    'status' => CampaignStatus::DRAFT,
    'uuid' => $faker->uuid,
    'last_modified_at' => now(),
    'email_list_id' => fn () => factory(config('mailcoach.models.email_list'))->create(['uuid' => (string)Str::uuid()]),
]);

<?php

use Faker\Generator;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Enums\CampaignStatus;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\EmailList;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Campaign::class, fn (Generator $faker) => [
    'subject' => $faker->sentence,
    'from_email' => $faker->email,
    'from_name' => $faker->name,
    'html' => $faker->randomHtml(),
    'track_opens' => $faker->boolean,
    'track_clicks' => $faker->boolean,
    'status' => CampaignStatus::DRAFT,
    'uuid' => $faker->uuid,
    'last_modified_at' => now(),
    'email_list_id' => fn () => factory(EmailList::class)->create(['uuid' => (string)Str::uuid()])
]);

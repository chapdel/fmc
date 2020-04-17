<?php

use Faker\Generator;
use Spatie\Mailcoach\Models\CampaignLink;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(CampaignLink::class, fn (Generator $faker) => [
    'campaign_id' => factory(config('mailcoach.models.campaign')),
    'link' => $faker->url,
]);

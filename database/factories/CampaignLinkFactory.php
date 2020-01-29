<?php

use Faker\Generator;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\CampaignLink;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(CampaignLink::class, fn (Generator $faker) => [
    'campaign_id' => factory(Campaign::class),
    'link' => $faker->url,
]);

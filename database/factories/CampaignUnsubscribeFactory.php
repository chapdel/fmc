<?php

use Faker\Generator;
use Spatie\Mailcoach\Models\CampaignUnsubscribe;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(CampaignUnsubscribe::class, fn (Generator $faker) => [
    'campaign_id' => factory(config('mailcoach.models.campaign')),
    'subscriber_id' => factory(config('mailcoach.models.subscriber')),
]);

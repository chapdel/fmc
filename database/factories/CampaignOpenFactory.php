<?php

use Faker\Generator;
use Spatie\Mailcoach\Models\CampaignOpen;
use Spatie\Mailcoach\Models\Send;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(CampaignOpen::class, fn (Generator $faker) => [
    'send_id' => factory(Send::class),
    'campaign_id' => factory(config('mailcoach.models.campaign')),
    'subscriber_id' => factory(config('mailcoach.models.subscriber')),
]);

<?php

use Faker\Generator;
use Spatie\Mailcoach\Models\CampaignClick;
use Spatie\Mailcoach\Models\CampaignLink;
use Spatie\Mailcoach\Models\Send;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(CampaignClick::class, fn (Generator $faker) => [
    'send_id' => factory(Send::class),
    'campaign_link_id' => factory(CampaignLink::class),
    'subscriber_id' => factory(config('mailcoach.models.subscriber')),
]);

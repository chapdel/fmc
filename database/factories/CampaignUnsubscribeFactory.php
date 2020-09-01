<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\CampaignUnsubscribe;
use Spatie\Mailcoach\Models\Subscriber;

class CampaignUnsubscribeFactory extends Factory
{
    protected $model = CampaignUnsubscribe::class;

    public function definition()
    {
        return [
            'campaign_id' => Campaign::factory(),
            'subscriber_id' => Subscriber::factory(),
        ];
    }
}

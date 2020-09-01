<?php

namespace Database\Factories;

use Spatie\Mailcoach\Models\CampaignClick;
use Spatie\Mailcoach\Models\CampaignLink;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Models\Subscriber;

class CampaignClickFactory extends Factory
{
    protected $model = CampaignClick::class;

    public function definition()
    {
        return [
            'send_id' => CampaignSendFactory::new(),
            'campaign_link_id' => CampaignLink::factory(),
            'subscriber_id' => Subscriber::factory(),
        ];
    }
}

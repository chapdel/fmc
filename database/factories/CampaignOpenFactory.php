<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignOpen;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;

class CampaignOpenFactory extends Factory
{
    protected $model = CampaignOpen::class;

    public function definition()
    {
        return [
            'send_id' => CampaignSendFactory::new(),
            'campaign_id' => Campaign::factory(),
            'subscriber_id' => Subscriber::factory(),
        ];
    }
}

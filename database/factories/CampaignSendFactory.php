<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Send;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;

class CampaignSendFactory extends Factory
{
    protected $model = Send::class;

    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid,
            'campaign_id' => Campaign::factory(),
            'subscriber_id' => Subscriber::factory(),
        ];
    }
}

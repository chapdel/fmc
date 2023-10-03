<?php

namespace Spatie\Mailcoach\Tests\Domain\Vendor\Ses\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;

class SendFactory extends Factory
{
    protected $model = Send::class;

    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'campaign_id' => new CampaignFactory(),
            'subscriber_id' => new SubscriberFactory(),
        ];
    }
}

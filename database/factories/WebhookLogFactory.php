<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookLog;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

class WebhookLogFactory extends Factory
{
    protected $model = WebhookLog::class;

    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'webhook_configuration_id' => WebhookConfiguration::factory(),
            'webhook_event_type' => $this->faker->randomElement([
                WebhookCallFailedEvent::class,
                WebhookCallSucceededEvent::class,
            ]),
            'event_type' => $this->faker->randomElement([
                'SubscribedEvent',
                'UnsubscribedEvent',
                'CampaignSentEvent',
                'TagAddedEvent',
                'TagRemovedEvent',
            ]),
            'webhook_call_uuid' => $this->faker->uuid,
            'attempt' => $this->faker->numberBetween(0, 5),
            'webhook_url' => $this->faker->url,
            'payload' => [],
            'response' => [],
            'status_code' => $this->faker->numberBetween(200, 500),
        ];
    }
}

<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SendFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return static::getSendClass();
    }

    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid,
            'content_item_id' => ContentItem::factory(),
            'subscriber_id' => Subscriber::factory(),
        ];
    }

    public function automationMail(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'content_item_id' => ContentItem::factory()->automationMail(),
            ];
        });
    }

    public function transactionalMailLogItem(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'content_item_id' => ContentItem::factory()->transactionalMailLogItem(),
            ];
        });
    }
}

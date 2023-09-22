<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Content\Models\Open;

class OpenFactory extends Factory
{
    protected $model = Open::class;

    public function definition()
    {
        return [
            'send_id' => SendFactory::new(),
            'content_item_id' => ContentItem::factory(),
            'subscriber_id' => Subscriber::factory(),
        ];
    }
}

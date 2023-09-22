<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Content\Models\Unsubscribe;

class UnsubscribeFactory extends Factory
{
    protected $model = Unsubscribe::class;

    public function definition()
    {
        return [
            'content_item_id' => ContentItem::factory(),
            'subscriber_id' => Subscriber::factory(),
        ];
    }
}

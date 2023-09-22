<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Content\Models\Click;
use Spatie\Mailcoach\Domain\Content\Models\Link;

class ClickFactory extends Factory
{
    protected $model = Click::class;

    public function definition()
    {
        return [
            'send_id' => SendFactory::new(),
            'link_id' => Link::factory(),
            'subscriber_id' => Subscriber::factory(),
        ];
    }
}

<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailClick;
use Spatie\Mailcoach\Domain\Campaign\Models\Send;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailClick;

class AutomationMailClickFactory extends Factory
{
    protected $model = AutomationMailClick::class;

    public function definition()
    {
        return [
            'send_id' => Send::factory(),
            'url' => $this->faker->url,
        ];
    }
}

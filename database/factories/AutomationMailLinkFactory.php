<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailClick;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailLink;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class AutomationMailLinkFactory extends Factory
{
    protected $model = AutomationMailLink::class;

    public function definition()
    {
        return [
            'automation_mail_id' => AutomationMail::factory(),
            'url' => $this->faker->url,
        ];
    }
}

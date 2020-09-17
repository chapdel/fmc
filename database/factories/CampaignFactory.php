<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Enums\CampaignStatus;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\EmailList;

class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    public function definition()
    {
        return [
            'subject' => $this->faker->sentence,
            'from_email' => $this->faker->email,
            'from_name' => $this->faker->name,
            'html' => $this->faker->randomHtml(),
            'track_opens' => $this->faker->boolean,
            'track_clicks' => $this->faker->boolean,
            'status' => CampaignStatus::DRAFT,
            'uuid' => $this->faker->uuid,
            'last_modified_at' => now(),
            'email_list_id' => EmailList::factory(),
        ];
    }
}

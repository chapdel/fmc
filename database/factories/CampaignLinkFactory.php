<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\CampaignLink;

class CampaignLinkFactory extends Factory
{
    protected $model = CampaignLink::class;

    public function definition()
    {
        return [
            'campaign_id' => Campaign::factory(),
            'url' => $this->faker->url,
        ];
    }
}

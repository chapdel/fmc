<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CampaignFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return static::getCampaignClass();
    }

    public function definition()
    {
        return [
            'subject' => $this->faker->sentence,
            'from_email' => $this->faker->email,
            'from_name' => $this->faker->name,
            'html' => $html = $this->faker->randomHtml(),
            'structured_html' => json_encode([
                'templateValues' => [
                    'html' => json_encode($html),
                ],
            ], JSON_THROW_ON_ERROR),
            'status' => CampaignStatus::Draft,
            'uuid' => $this->faker->uuid,
            'last_modified_at' => now(),
            'email_list_id' => EmailList::factory(),
            'template_id' => Template::factory(),
        ];
    }

    public function emptyDraft(string $templateId = null): Factory
    {
        return $this->state(function (array $attributes) use ($templateId) {
            return [
                'from_email' => null,
                'from_name' => null,
                'reply_to_email' => null,
                'reply_to_name' => null,
                'template_id' => $templateId,
                'html' => null,
                'structured_html' => json_encode([
                    'templateValues' => [
                        'html' => null,
                    ],
                ], JSON_THROW_ON_ERROR),
            ];
        });
    }
}

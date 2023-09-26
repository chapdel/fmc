<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\Template\Models\Template;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;

class ContentItemFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return static::getContentItemClass();
    }

    public function definition()
    {
        return [
            'model_type' => (new Campaign())->getMorphClass(),
            'model_id' => Campaign::factory()->recycle($this),
            'subject' => $this->faker->sentence,
            'from_email' => $this->faker->email,
            'from_name' => $this->faker->name,
            'html' => $html = $this->faker->randomHtml(),
            'structured_html' => json_encode([
                'templateValues' => [
                    'html' => json_encode($html),
                ],
            ], JSON_THROW_ON_ERROR),
            'uuid' => $this->faker->uuid,
            'template_id' => Template::factory(),
        ];
    }

    public function automationMail(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'model_type' => (new AutomationMail())->getMorphClass(),
                'model_id' => AutomationMail::factory()->recycle($this),
            ];
        });
    }

    public function transactionalMailLogItem(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'model_type' => (new TransactionalMailLogItem())->getMorphClass(),
                'model_id' => TransactionalMailLogItem::factory()->recycle($this),
            ];
        });
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

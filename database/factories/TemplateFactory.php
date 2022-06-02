<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class TemplateFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return static::getTemplateClass();
    }

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'html' => $this->faker->randomHtml(),
        ];
    }
}

<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class TagFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return self::getTagClass();
    }

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->words(asText: true),
        ];
    }
}

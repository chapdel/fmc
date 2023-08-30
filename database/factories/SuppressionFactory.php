<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SuppressionFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName(): string
    {
        return static::getSuppressionClass();
    }

    public function definition(): array
    {
        return [
            'email' => $this->faker->email,
            'uuid' => $this->faker->uuid,
            'reason' => 'HardBounce',
            'origin' => $this->faker->randomElement(['recipient', 'admin']),
            'stream' => $this->faker->randomElement(['outbound', 'broadcast', 'transactional']),
        ];
    }
}

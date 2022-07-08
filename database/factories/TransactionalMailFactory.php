<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\LaravelRay\Tests\TestClasses\TestMailable;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

class TransactionalMailFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return static::getTransactionalMailClass();
    }

    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid,
            'subject' => $this->faker->sentence,
            'body' => $this->faker->randomHtml(),
            'from' => [$this->person()],
            'to' => [$this->person()],
            'cc' => [$this->person()],
            'bcc' => [$this->person()],
            'mailable_class' => TestMailable::class,
        ];
    }

    protected function person(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];
    }
}

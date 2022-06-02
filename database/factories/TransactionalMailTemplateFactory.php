<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\Mailcoach\Tests\TestClasses\TestMailableWithTemplate;

class TransactionalMailTemplateFactory extends Factory
{
    use UsesMailcoachModels;

    public function modelName()
    {
        return static::getTransactionalMailTemplateClass();
    }

    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid,
            'name' => $this->faker->word,
            'subject' => $this->faker->sentence,
            'from' => $this->faker->email,
            'to' => [$this->faker->email],
            'cc' => [$this->faker->email],
            'bcc' => [$this->faker->email],
            'body' => $this->faker->randomHtml(),
            'type' => 'blade',
            'track_opens' => true,
            'track_clicks' => true,
            'test_using_mailable' => TestMailableWithTemplate::class,
        ];
    }
}

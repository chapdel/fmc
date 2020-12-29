<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Campaign\Models\Send;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailClick;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailOpen;

class TransactionalMailOpenFactory extends Factory
{
    protected $model = TransactionalMailOpen::class;

    public function definition()
    {
        return [
            'send_id' => Send::factory(),
        ];
    }
}

<?php

namespace Spatie\Mailcoach\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\TagSegment;

class TagSegmentFactory extends Factory
{
    protected $model = TagSegment::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'email_list_id' => EmailList::factory(),
        ];
    }
}

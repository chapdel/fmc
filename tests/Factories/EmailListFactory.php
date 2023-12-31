<?php

namespace Spatie\Mailcoach\Tests\Factories;

use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;

class EmailListFactory
{
    protected int $subscriberCount = 0;

    public function withSubscriberCount(int $subscriberCount)
    {
        $this->subscriberCount = $subscriberCount;

        return $this;
    }

    public function create(array $attributes = []): EmailList
    {
        $emailList = EmailList::factory()->create($attributes);

        Collection::times($this->subscriberCount)
            ->each(function (int $i) use ($emailList) {
                $emailList->subscribe("subscriber{$i}@example.com");
            });

        return $emailList->refresh();
    }
}

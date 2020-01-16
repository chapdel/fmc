<?php

namespace Spatie\Mailcoach\Tests\Factories;

use Illuminate\Support\Collection;
use Spatie\Mailcoach\Models\EmailList;

class EmailListFactory
{
    /** @var int */
    private int $subscriberCount = 0;

    public function withSubscriberCount(int $subscriberCount)
    {
        $this->subscriberCount = $subscriberCount;

        return $this;
    }

    public function create(array $attributes = []): EmailList
    {
        $emailList = factory(EmailList::class)->create($attributes);

        Collection::times($this->subscriberCount)
            ->each(function (int $i) use ($emailList) {
                $emailList->subscribe("subscriber{$i}@example.com");
            });

        return $emailList->refresh();
    }
}

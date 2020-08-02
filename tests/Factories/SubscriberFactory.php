<?php

namespace Spatie\Mailcoach\Tests\Factories;

use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;

class SubscriberFactory
{
    private EmailList $emailList;

    public static function new(): self
    {
        return new static();
    }

    public function __construct()
    {
        $this->emailList = factory(EmailList::class)->create(['requires_confirmation' => false]);
    }

    public function unconfirmed(): self
    {
        $this->emailList->update(['requires_confirmation' => true]);

        return $this;
    }

    public function confirmed(): self
    {
        $this->emailList->update(['requires_confirmation' => false]);

        return $this;
    }

    public function create(): Subscriber
    {
        return Subscriber::createWithEmail('john@example.com')->subscribeTo($this->emailList);
    }
}

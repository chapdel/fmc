<?php

namespace Spatie\Mailcoach\Tests\Actions;

use Spatie\Mailcoach\Domain\Campaign\Actions\Subscribers\UpdateSubscriberAction;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Support\Config;
use Spatie\Mailcoach\Tests\TestCase;

class UpdateSubscriberActionTest extends TestCase
{
    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Subscriber */
    private $subscriber;

    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\EmailList */
    private $emailList;

    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\EmailList */
    private $anotherEmailList;

    /** @var array */
    private $newAttributes;

    public function setUp(): void
    {
        parent::setUp();

        $this->subscriber = Subscriber::factory()->create();

        $this->emailList = EmailList::factory()->create();

        $this->anotherEmailList = EmailList::factory()->create();

        $this->newAttributes = [
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];
    }

    /** @test */
    public function it_can_update_the_attributes_of_a_subscriber()
    {
        $updateSubscriberAction = Config::getActionClass('update_subscriber', UpdateSubscriberAction::class);

        $updateSubscriberAction->execute(
            $this->subscriber,
            $this->newAttributes,
        );

        $this->subscriber->refresh();

        $this->assertEquals('john@example.com', $this->subscriber->email);
        $this->assertEquals('John', $this->subscriber->first_name);
        $this->assertEquals('Doe', $this->subscriber->last_name);
    }
}

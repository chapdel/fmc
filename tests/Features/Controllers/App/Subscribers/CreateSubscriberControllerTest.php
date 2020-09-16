<?php

namespace Spatie\Mailcoach\Tests\Features\Controllers\App\Subscribers;

use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\CreateSubscriberController;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;

class CreateSubscriberControllerTest extends TestCase
{
    /** @test */
    public function it_can_create_a_subscriber()
    {
        $this->authenticate();

        /** @var EmailList $emailList */
        $emailList = EmailList::factory()->create();

        $attributes = [
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];

        $this
            ->post(action([CreateSubscriberController::class, 'store'], $emailList), $attributes)
            ->assertSessionHasNoErrors();

        /** @var \Spatie\Mailcoach\Models\Subscriber $subscriber */
        $subscriber = Subscriber::first();

        $this->assertEquals('john@example.com', $subscriber->email);
        $this->assertEquals('John', $subscriber->first_name);
        $this->assertEquals('Doe', $subscriber->last_name);

        $this->assertTrue($emailList->isSubscribed($subscriber->email));
    }
}

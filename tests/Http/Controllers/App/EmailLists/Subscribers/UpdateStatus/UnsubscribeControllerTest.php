<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\App\EmailLists\Subscribers\UpdateStatus;

use Spatie\Mailcoach\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;

class UnsubscribeControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->authenticate();
    }

    /** @test */
    public function it_can_unsubscribe_a_subscriber()
    {
        $emailList = factory(EmailList::class)->create();

        $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);

        $this->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscriber->status);

        $this
            ->post(route('mailcoach.subscriber.unsubscribe', $subscriber))
            ->assertRedirect();

        $this->assertEquals(SubscriptionStatus::UNSUBSCRIBED, $subscriber->refresh()->status);
    }

    /** @test */
    public function it_will_only_unsubscribe_subscribed_subscribers()
    {
        $this->withExceptionHandling();

        $emailList = factory(EmailList::class)->create();
        $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);
        $subscriber->unsubscribe();

        $this->assertEquals(SubscriptionStatus::UNSUBSCRIBED, $subscriber->status);

        $this
            ->post(route('mailcoach.subscriber.unsubscribe', $subscriber))
            ->assertSessionHas('laravel_flash_message.class', 'error');
    }
}

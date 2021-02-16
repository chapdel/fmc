<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\App\EmailLists\Subscribers\UpdateStatus;

use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;

class ResubscribeControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->authenticate();
    }

    /** @test */
    public function it_can_confirm_a_subscriber()
    {
        $emailList = EmailList::factory()->create();

        $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);
        $subscriber->unsubscribe();

        $this->assertEquals(SubscriptionStatus::UNSUBSCRIBED, $subscriber->status);

        $this
            ->post(route('mailcoach.subscriber.resubscribe', $subscriber))
            ->assertRedirect();

        $this->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscriber->refresh()->status);
    }

    /** @test */
    public function it_will_only_resubscribe_unsubscribed_subscribers()
    {
        $this->withExceptionHandling();
        $emailList = EmailList::factory()->create();
        $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);
        $this->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscriber->status);

        $this
            ->post(route('mailcoach.subscriber.resubscribe', $subscriber))
            ->assertSessionHas('laravel_flash_message.class', 'error');
    }
}

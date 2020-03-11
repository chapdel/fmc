<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\App\EmailLists\Subscribers\UpdateStatus;

use Spatie\Mailcoach\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ConfirmControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->authenticate();
    }

    /** @test */
    public function it_can_confirm_a_subscriber()
    {
        $emailList = factory(EmailList::class)->create(['requires_confirmation' => true]);

        $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);

        $this->assertEquals(SubscriptionStatus::UNCONFIRMED, $subscriber->status);

        $this
            ->post(route('mailcoach.subscriber.confirm', $subscriber))
            ->assertRedirect();

        $this->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscriber->refresh()->status);
    }

    /** @test */
    public function it_will_confirm_unconfirmed_subscribers()
    {
        $this->withExceptionHandling();

        $subscriber = factory(Subscriber::class)->create([
            'unsubscribed_at' => now(),
            'subscribed_at' => now(),
        ]);

        $this->assertEquals(SubscriptionStatus::UNSUBSCRIBED, $subscriber->status);

        $this
            ->post(route('mailcoach.subscriber.confirm', $subscriber))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

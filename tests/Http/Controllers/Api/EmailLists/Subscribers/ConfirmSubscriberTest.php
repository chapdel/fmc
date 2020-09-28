<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\EmailLists\Subscribers;

use Illuminate\Http\Response;
use Spatie\Mailcoach\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\ConfirmSubscriberController;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class ConfirmSubscriberTest extends TestCase
{
    use RespondsToApiRequests;

    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->loginToApi();
    }

    /** @test */
    public function it_can_confirm_a_subscriber_using_the_api()
    {
        $subscriber = SubscriberFactory::new()->unconfirmed()->create();

        $this
            ->postJson(action(ConfirmSubscriberController::class, $subscriber))
            ->assertSuccessful();

        $this->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscriber->refresh()->status);
    }

    /** @test */
    public function it_will_not_confirm_a_subscriber_that_was_already_confirmed()
    {
        $subscriber = SubscriberFactory::new()->confirmed()->create();

        $this
            ->postJson(action(ConfirmSubscriberController::class, $subscriber))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\EmailLists\Subscribers;

use Illuminate\Http\Response;
use Spatie\Mailcoach\Domain\Campaign\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\UnsubscribeController;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class UnsubscribeControllerTest extends TestCase
{
    use RespondsToApiRequests;

    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->loginToApi();
    }

    /** @test */
    public function it_can_unsubscribe_a_subscriber_via_the_api()
    {
        $subscriber = SubscriberFactory::new()->confirmed()->create();

        $this
            ->postJson(action(UnsubscribeController::class, $subscriber))
            ->assertSuccessful();

        $this->assertEquals(SubscriptionStatus::UNSUBSCRIBED, $subscriber->refresh()->status);
    }

    /** @test */
    public function it_cannot_unsubscribe_someone_that_is_already_unsubscribed()
    {
        $subscriber = SubscriberFactory::new()->unsubscribed()->create();

        $this
            ->postJson(action(UnsubscribeController::class, $subscriber))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

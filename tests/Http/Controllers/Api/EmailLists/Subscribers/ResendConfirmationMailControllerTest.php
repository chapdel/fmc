<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\EmailLists\Subscribers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\ResendConfirmationMailController;
use Spatie\Mailcoach\Domain\Campaign\Mails\ConfirmSubscriberMail;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class ResendConfirmationMailControllerTest extends TestCase
{
    use RespondsToApiRequests;

    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->loginToApi();

        Mail::fake();
    }

    /** @test */
    public function it_can_resend_the_confirmation_mail()
    {
        $subscriber = SubscriberFactory::new()->unconfirmed()->create();

        $this
            ->postJson(action(ResendConfirmationMailController::class, $subscriber))
            ->assertSuccessful();

        Mail::assertQueued(ConfirmSubscriberMail::class);
    }

    /** @test */
    public function it_cannot_resend_the_confirmation_mail_to_a_subscriber_that_is_not_unconfirmed()
    {
        $subscriber = SubscriberFactory::new()->confirmed()->create();

        $this
            ->postJson(action(ResendConfirmationMailController::class, $subscriber))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        Mail::assertNotQueued(ConfirmSubscriberMail::class);
    }
}

<?php

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Events\SubscriberSuppressedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\Suppression;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignMailSentEvent;
use Spatie\Mailcoach\Domain\Content\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Actions\SendMailAction;
use Spatie\Mailcoach\Domain\Shared\Enums\SendFeedbackType;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Models\SendFeedbackItem;

beforeEach(function () {
    $this->action = resolve(SendMailAction::class);

    /** @var Send $send */
    $this->send = Send::factory()->create();
});

it('sends a pending send', function () {
    Mail::fake();
    Event::fake();

    $this->action->execute($this->send);

    Mail::assertSent(MailcoachMail::class, function (MailcoachMail $mail) {
        expect($mail->hasTo($this->send->subscriber->email))->toBeTrue();

        return true;
    });

    Event::assertDispatched(CampaignMailSentEvent::class);

    expect($this->send->wasAlreadySent())->toBeTrue();
});

it('wont send again if the send was already sent', function () {
    Mail::fake();
    Event::fake();

    $this->action->execute($this->send);
    $this->action->execute($this->send);

    Mail::assertSent(MailcoachMail::class, 1);
    Event::assertDispatched(CampaignMailSentEvent::class, 1);
});

it('sets message headers', function () {
    $assertionsPassed = false;

    Event::listen(MessageSent::class, function (MessageSent $event) use (&$assertionsPassed) {
        /** @var \Symfony\Component\Mime\Header\Headers $headers */
        $headers = $event->message->getHeaders();

        expect($headers->get('X-MAILCOACH')->getBody())->toBe('true');
        expect($headers->get('Precedence')->getBody())->toBe('Bulk');
        expect($headers->get('X-PM-Metadata-send-uuid')->getBody())->not()->toBeEmpty();

        $assertionsPassed = true;
    });

    $this->action->execute($this->send);

    expect($assertionsPassed)->toBeTrue();
});

it('will not sent to subscribers registered as suppressed', function () {
    Mail::fake();
    Event::fake();

    $suppressed = Suppression::factory()->create(['email' => 'invalid@example.com']);
    $subscriber = Subscriber::factory()->create(['email' => $suppressed->email]);

    $this->send = Send::factory()->create([
        'subscriber_id' => $subscriber->id,
    ]);

    $this->action->execute($this->send);

    Mail::assertNothingSent();
    Event::assertNotDispatched(CampaignMailSentEvent::class);
    Event::assertDispatched(SubscriberSuppressedEvent::class, 1);

    expect($this->send->wasAlreadySent())->toBeTrue();
    expect($subscriber->refresh()->unsubscribed_at)->not()->toBeNull();

    $this->assertDatabaseHas(SendFeedbackItem::getSendFeedbackItemTableName(), [
        'send_id' => $this->send->id,
        'type' => SendFeedbackType::Suppressed,
    ]);
});

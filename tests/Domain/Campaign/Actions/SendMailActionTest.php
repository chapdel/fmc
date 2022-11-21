<?php

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Campaign\Actions\SendMailAction;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignMailSentEvent;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

beforeEach(function () {
    $this->action = resolve(SendMailAction::class);

    /** @var Send $send */
    $this->send = Send::factory()->create(['automation_mail_id' => null]);
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

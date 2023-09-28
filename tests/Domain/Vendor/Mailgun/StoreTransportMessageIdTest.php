<?php

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\SentMessage;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\TextPart;

it('can store a message_id from the transport', function () {
    $pendingSend = Send::factory()->create();
    $message = (new Email())->setBody(new TextPart('body'));
    $message->getHeaders()->addTextHeader('X-Mailgun-Message-ID', '1234');
    $message->sender('john@doe.com');
    $message->to('john@doe.com');
    $message->text('body');

    $symfonySentMessage = new \Symfony\Component\Mailer\SentMessage(
        message: $message,
        envelope: new Envelope(
            sender: new Address('john@doe.com'),
            recipients: [new Address('john@doe.com')]
        )
    );

    $message = new SentMessage($symfonySentMessage);

    event(new MessageSent($message, [
        'send' => $pendingSend,
    ]));

    tap($pendingSend->fresh(), function (Send $send) {
        expect($send->transport_message_id)->not->toBeNull();
    });
});

<?php

use Illuminate\Mail\Events\MessageSending;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\TextPart;

it('adds a message stream header if the message is sent by mailcoach', function () {
    $message = (new Email())->setBody(new TextPart('body'));
    $message->getHeaders()->addTextHeader('X-MAILCOACH', true);

    config()->set('mailcoach.postmark_feedback.message_stream', 'hello');
    config()->set('mail.default', 'postmark');
    config()->set('mail.mailers.ses.transport', 'postmark');

    event(new MessageSending($message));

    expect($message->getHeaders()->get('X-PM-Message-Stream')?->getBodyAsString())->toEqual('hello');
});

<?php

use Illuminate\Mail\Events\MessageSending;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\TextPart;

it('adds a configuration set header if the message is sent by mailcoach', function () {
    $message = (new Email())->setBody(new TextPart('body'));
    $message->getHeaders()->addTextHeader('X-MAILCOACH', true);

    config()->set('mailcoach.ses_feedback.configuration_set', 'hello');
    config()->set('mail.default', 'ses');
    config()->set('mail.mailers.ses.transport', 'ses');

    event(new MessageSending($message));

    expect($message->getHeaders()->get('X-SES-CONFIGURATION-SET')->getBodyAsString())->toEqual('hello');
});

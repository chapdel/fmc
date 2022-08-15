<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;

it('will set transport id', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();

    $campaignMailable = (new MailcoachMail())
        ->setSendable($send->campaign)
        ->setSend($send)
        ->setHtmlContent('dummy content')
        ->subject('test mail');

    Mail::to('john@example.com')->send($campaignMailable);

    $domain = '@'.Str::after($campaignMailable->from[0]['address'], '@');

    test()->assertStringEndsWith(
        $domain,
        $send->refresh()->transport_message_id
    );
});

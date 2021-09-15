<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Shared\Mails\MailcoachMail;
use Spatie\Mailcoach\Tests\TestCase;

it('will set transport id', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();

    $campaignMailable = (new MailcoachMail())
        ->setSendable($send->campaign)
        ->setSend($send)
        ->setHtmlContent('dummy content')
        ->subject('test mail');

    Mail::to('john@example.com')->send($campaignMailable);

    test()->assertStringEndsWith(
        '@swift.generated',
        $send->refresh()->transport_message_id
    );
});

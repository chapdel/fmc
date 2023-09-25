<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Content\Mails\MailcoachMail;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;

it('will set transport id', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();

    $campaignMailable = (new MailcoachMail())
        ->setContentItem($send->contentItem)
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

it('can send to multiple reply-to users', function () {
    $campaign = (new CampaignFactory())->create();

    $campaign->emailList->update([
        'default_reply_to_email' => 'jan@example.com, piet@example.com',
        'default_reply_to_name' => 'Jan, Piet',
    ]);

    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create([
        'content_item_id' => $campaign->contentItem->id,
    ]);

    $campaignMailable = (new MailcoachMail())
        ->setContentItem($send->contentItem)
        ->setSend($send)
        ->setHtmlContent('dummy content')
        ->subject('test mail')
        ->build();

    test()->assertSame([
        [
            'name' => 'Jan',
            'address' => 'jan@example.com',
        ],
        [
            'name' => 'Piet',
            'address' => 'piet@example.com',
        ],
    ], $campaignMailable->replyTo);
});

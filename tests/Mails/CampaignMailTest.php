<?php

namespace Spatie\Mailcoach\Tests\Mails;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Mails\CampaignMail;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;

class CampaignMailTest extends TestCase
{
    /** @test */
    public function it_will_set_transport_id()
    {
        /** @var \Spatie\Mailcoach\Models\Send $send */
        $send = factory(Send::class)->create();

        $campaignMailable = (new CampaignMail())
            ->setCampaign($send->campaign)
            ->setSend($send)
            ->setHtmlContent('dummy content')
            ->subject('test mail');

        Mail::to('john@example.com')->send($campaignMailable);

        $this->assertStringEndsWith(
            '@swift.generated',
            $send->refresh()->transport_message_id
        );
    }
}

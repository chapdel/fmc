<?php

namespace Spatie\Mailcoach\Tests\Mails;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Database\Factories\CampaignSendFactory;
use Spatie\Mailcoach\Domain\Campaign\Mails\CampaignMail;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CampaignMailTest extends TestCase
{
    use MatchesSnapshots;

    /** @test */
    public function it_will_set_transport_id()
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Send $send */
        $send = CampaignSendFactory::new()->create();

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

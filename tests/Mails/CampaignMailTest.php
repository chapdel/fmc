<?php

namespace Spatie\Mailcoach\Tests\Mails;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Jobs\SendMailJob;
use Spatie\Mailcoach\Mails\CampaignMail;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CampaignMailTest extends TestCase
{
    use MatchesSnapshots;

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

    /** @test */
    public function it_will_use_8_bit_encoding_for_the_plain_text_part()
    {
        /** @var Send $send */
        $send = factory(Send::class)->create();

        $send->update(['uuid' => 'my-uuid']);

        $send->campaign->update([
            'email_html' => '<html><ul><li>hey hey 😀</li></ul></html>',
            'subject' => 'My subject',
            'from_email' => 'john@example.com',
            'from_name' => 'John Doe',
        ]);

        $send->subscriber->update(['email' => 'freek@spatie.be']);

        $sentMessageHtml = '';

        Event::listen(MessageSent::class, function (MessageSent $event) use (&$sentMessageHtml) {
            $sentMessageHtml = $event->message->toString();
        });

        dispatch(new SendMailJob($send->refresh()));

        $lines = explode(PHP_EOL, $sentMessageHtml);

        $sentMessageHtml = collect($lines)
            ->reject(function (string $line) use ($sentMessageHtml) {
                return Str::startsWith($line, [
                    'Message-ID',
                    'Date',
                    ' boundary',
                    'List-Unsubscribe',
                    '--_=_swift'
                ]);
            })
            ->implode(PHP_EOL);

        $this->assertMatchesSnapshot($sentMessageHtml);
    }
}

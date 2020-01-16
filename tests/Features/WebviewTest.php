<?php

namespace Spatie\Mailcoach\Tests\Features;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class WebviewTest extends TestCase
{
    /** @var string */
    private string $webviewUrl;

    public function setUp(): void
    {
        parent::setUp();

        $this->campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
            'html' => 'My campaign <a href="::webviewUrl::">Web view</a>',
            'track_clicks' => true,
        ]);

        $this->emailList = $this->campaign->emailList;

        $this->subscriber = $this->campaign->emailList->subscribers->first();
    }

    /** @test */
    public function it_can_sends_links_to_webviews()
    {
        $this->sendCampaign();

        $this
            ->get($this->webviewUrl)
            ->assertSuccessful()
            ->assertSee('My campaign');
    }

    protected function sendCampaign()
    {
        Event::listen(MessageSent::class, function (MessageSent $event) {
            $link = (new Crawler($event->message->getBody()))
                ->filter('a')->first()->attr('href');

            $this->assertStringStartsWith('http://localhost', $link);

            $this->webviewUrl = Str::after($link, 'http://localhost');
        });

        dispatch(new SendCampaignJob($this->campaign));
    }
}

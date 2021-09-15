<?php

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Symfony\Component\DomCrawler\Crawler;



beforeEach(function () {
    test()->campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
        'html' => 'My campaign <a href="::webviewUrl::">Web view</a>',
        'track_clicks' => true,
    ]);

    test()->emailList = test()->campaign->emailList;

    test()->subscriber = test()->campaign->emailList->subscribers->first();
});

it('can sends links to webviews', function () {
    sendCampaignForWebviewTest();

    $this
        ->get(test()->webviewUrl)
        ->assertSuccessful()
        ->assertSee('My campaign');
});

// Helpers
function sendCampaignForWebviewTest()
{
    Event::listen(MessageSent::class, function (MessageSent $event) {
        $link = (new Crawler($event->message->getBody()))
            ->filter('a')->first()->attr('href');

        expect($link)->toStartWith('http://localhost');

        test()->webviewUrl = Str::after($link, 'http://localhost');
    });

    dispatch(new SendCampaignJob(test()->campaign));
}

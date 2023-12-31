<?php

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Symfony\Component\DomCrawler\Crawler;

beforeEach(function () {
    test()->tagName = 'some tag {foo}';

    test()->campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
        'html' => '<a href="::unsubscribeTag::'.urlencode(test()->tagName).'::">Unsubscribe</a>',
    ]);

    test()->emailList = test()->campaign->emailList;

    test()->subscriber = test()->campaign->emailList->subscribers->first();
    test()->subscriber->addTag(test()->tagName);
});

it('can render the unsubscribe confirmation page', function () {
    sendCampaignForUnsubscribeTagTest();

    expect(test()->subscriber->status)->toEqual(SubscriptionStatus::Subscribed);

    $this
        ->get(test()->mailedUnsubscribeLink)
        ->assertSuccessful()
        ->assertViewIs('mailcoach::landingPages.unsubscribe-tag');
});

it('can unsubscribe from a tag', function () {
    sendCampaignForUnsubscribeTagTest();

    expect(test()->subscriber->status)->toEqual(SubscriptionStatus::Subscribed);

    expect(test()->subscriber->hasTag(test()->tagName))->toBeTrue();

    $content = $this
        ->post(test()->mailedUnsubscribeLink)
        ->assertSuccessful()
        ->baseResponse->content();

    expect($content)->toContain('unsubscribed');

    expect(test()->subscriber->fresh()->hasTag(test()->tagName))->toBeFalse();
});

it('will redirect to the unsubscribed view by default', function () {
    sendCampaignForUnsubscribeTagTest();

    $this
        ->post(test()->mailedUnsubscribeLink)
        ->assertSuccessful()
        ->assertViewIs('mailcoach::landingPages.unsubscribed-tag');
});

it('will redirect to the unsubscribed url if it has been set on the email list', function () {
    $url = 'https://example.com/unsubscribed';
    test()->campaign->emailList->update(['redirect_after_unsubscribed' => $url]);

    sendCampaignForUnsubscribeTagTest();

    $this
        ->post(test()->mailedUnsubscribeLink)
        ->assertRedirect($url);
});

test('the unsubscribe will work even if the send is deleted', function () {
    sendCampaignForUnsubscribeTagTest();

    Send::all()->each->delete();

    test()->post(test()->mailedUnsubscribeLink)->assertSuccessful();

    expect(test()->subscriber->fresh()->hasTag(test()->tagName))->toBeFalse();
});

// Helpers
function sendCampaignForUnsubscribeTagTest()
{
    Event::listen(MessageSent::class, function (MessageSent $event) {
        $link = (new Crawler($event->message->getHtmlBody()))
            ->filter('a')->first()->attr('href');

        expect($link)->toStartWith('http://localhost');

        test()->mailedUnsubscribeLink = Str::after($link, 'http://localhost');
    });

    test()->campaign->send();
    Artisan::call('mailcoach:send-scheduled-campaigns');
    Artisan::call('mailcoach:send-campaign-mails');
}

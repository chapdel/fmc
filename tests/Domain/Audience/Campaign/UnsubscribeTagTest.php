<?php

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Symfony\Component\DomCrawler\Crawler;

beforeEach(function () {
    test()->campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
        'html' => '<a href="::unsubscribeTag::some tag::">Unsubscribe</a>',
    ]);

    test()->emailList = test()->campaign->emailList;

    test()->subscriber = test()->campaign->emailList->subscribers->first();
    test()->subscriber->addTag('some tag');
});

it('can render the unsubscribe confirmation page', function () {
    sendCampaignForUnsubscribeTagTest();

    expect(test()->subscriber->status)->toEqual(SubscriptionStatus::SUBSCRIBED);

    $this
        ->get(test()->mailedUnsubscribeLink)
        ->assertSuccessful()
        ->assertViewIs('mailcoach::landingPages.unsubscribe-tag');
});

it('can unsubscribe from a tag', function () {
    sendCampaignForUnsubscribeTagTest();

    expect(test()->subscriber->status)->toEqual(SubscriptionStatus::SUBSCRIBED);

    expect(test()->subscriber->hasTag('some tag'))->toBeTrue();

    $content = $this
        ->post(test()->mailedUnsubscribeLink)
        ->assertSuccessful()
        ->baseResponse->content();

    expect($content)->toContain('unsubscribed');

    expect(test()->subscriber->fresh()->hasTag('some tag'))->toBeFalse();
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

    expect(test()->subscriber->fresh()->hasTag('some tag'))->toBeFalse();
});

// Helpers
function sendCampaignForUnsubscribeTagTest()
{
    Event::listen(MessageSent::class, function (MessageSent $event) {
        $link = (new Crawler($event->message->getBody()))
            ->filter('a')->first()->attr('href');

        expect($link)->toStartWith('http://localhost');

        test()->mailedUnsubscribeLink = Str::after($link, 'http://localhost');
    });

    dispatch(new SendCampaignJob(test()->campaign));
}

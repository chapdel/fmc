<?php

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignUnsubscribe;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Http\Front\Controllers\UnsubscribeController;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Symfony\Component\DomCrawler\Crawler;

beforeEach(function () {
    test()->campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
        'html' => '<a href="::unsubscribeUrl::">Unsubscribe</a>',
    ]);

    test()->emailList = test()->campaign->emailList;

    test()->subscriber = test()->campaign->emailList->subscribers->first();
});

it('can render the unsubscribe confirmation page', function () {
    sendCampaign();

    expect(test()->subscriber->status)->toEqual(SubscriptionStatus::SUBSCRIBED);

    $this
        ->get(test()->mailedUnsubscribeLink)
        ->assertSuccessful()
        ->assertViewIs('mailcoach::landingPages.unsubscribe');
});

it('can unsubscribe from a list', function () {
    sendCampaign();

    expect(test()->subscriber->status)->toEqual(SubscriptionStatus::SUBSCRIBED);

    $content = $this
        ->post(test()->mailedUnsubscribeLink)
        ->assertSuccessful()
        ->baseResponse->content();

    expect($content)->toContain('unsubscribed');

    expect(test()->subscriber->refresh()->status)->toEqual(SubscriptionStatus::UNSUBSCRIBED);

    expect(CampaignUnsubscribe::all())->toHaveCount(1);
    $campaignUnsubscribe = CampaignUnsubscribe::first();

    expect($campaignUnsubscribe->subscriber->uuid)->toEqual(test()->subscriber->uuid);
    expect($campaignUnsubscribe->campaign->uuid)->toEqual(test()->campaign->uuid);

    $subscription = test()->emailList->allSubscribers()->first();
    expect($subscription->status)->toEqual(SubscriptionStatus::UNSUBSCRIBED);
});

it('will redirect to the unsubscribed view by default', function () {
    sendCampaign();

    $this
        ->post(test()->mailedUnsubscribeLink)
        ->assertSuccessful()
        ->assertViewIs('mailcoach::landingPages.unsubscribed');
});

it('will redirect to the unsubscribed url if it has been set on the email list', function () {
    $url = 'https://example.com/unsubscribed';
    test()->campaign->emailList->update(['redirect_after_unsubscribed' => $url]);

    sendCampaign();

    $this
        ->post(test()->mailedUnsubscribeLink)
        ->assertRedirect($url);
});

it('will only store a single unsubscribe even if the unsubscribe link is used multiple times', function () {
    sendCampaign();

    test()->post(test()->mailedUnsubscribeLink)->assertSuccessful();
    $response = test()->get(test()->mailedUnsubscribeLink)->assertSuccessful()->baseResponse->content();

    expect(CampaignUnsubscribe::all())->toHaveCount(1);

    expect($response)->toContain('already unsubscribed');
});

test('the unsubscribe will work even if the send is deleted', function () {
    sendCampaign();

    Send::all()->each->delete();

    test()->post(test()->mailedUnsubscribeLink)->assertSuccessful();

    expect(test()->subscriber->refresh()->status)->toEqual(SubscriptionStatus::UNSUBSCRIBED);
});

test('the unsubscribe header is added to the email', function () {
    Event::listen(MessageSent::class, function (MessageSent $event) {
        $subscription = test()->emailList->allSubscribers()->first();

        test()->assertNotNull($event->message->getHeaders()->get('List-Unsubscribe'));

        expect($event->message->getHeaders()->get('List-Unsubscribe')->getValue())->toEqual('<'.url(action(UnsubscribeController::class, [$subscription->uuid, Send::first()->uuid])).'>');

        test()->assertNotNull($event->message->getHeaders()->get('List-Unsubscribe-Post'));

        expect($event->message->getHeaders()->get('List-Unsubscribe-Post')->getValue())->toEqual('List-Unsubscribe=One-Click');
    });

    dispatch(new SendCampaignJob(test()->campaign));
});

// Helpers
function sendCampaign()
{
    Event::listen(MessageSent::class, function (MessageSent $event) {
        $link = (new Crawler($event->message->getBody()))
            ->filter('a')->first()->attr('href');

        expect($link)->toStartWith('http://localhost');

        test()->mailedUnsubscribeLink = Str::after($link, 'http://localhost');
    });

    dispatch(new SendCampaignJob(test()->campaign));
}

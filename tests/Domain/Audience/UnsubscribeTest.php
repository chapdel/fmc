<?php

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignUnsubscribe;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Http\Front\Controllers\UnsubscribeController;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Symfony\Component\DomCrawler\Crawler;

uses(TestCase::class);

beforeEach(function () {
    test()->campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
        'html' => '<a href="::unsubscribeUrl::">Unsubscribe</a>',
    ]);

    test()->emailList = test()->campaign->emailList;

    test()->subscriber = test()->campaign->emailList->subscribers->first();
});

it('can render the unsubscribe confirmation page', function () {
    sendCampaign();

    test()->assertEquals(SubscriptionStatus::SUBSCRIBED, test()->subscriber->status);

    $this
        ->get(test()->mailedUnsubscribeLink)
        ->assertSuccessful()
        ->assertViewIs('mailcoach::landingPages.unsubscribe');
});

it('can unsubscribe from a list', function () {
    sendCampaign();

    test()->assertEquals(SubscriptionStatus::SUBSCRIBED, test()->subscriber->status);

    $content = $this
        ->post(test()->mailedUnsubscribeLink)
        ->assertSuccessful()
        ->baseResponse->content();

    test()->assertStringContainsString('unsubscribed', $content);

    test()->assertEquals(SubscriptionStatus::UNSUBSCRIBED, test()->subscriber->refresh()->status);

    test()->assertCount(1, CampaignUnsubscribe::all());
    $campaignUnsubscribe = CampaignUnsubscribe::first();

    test()->assertEquals(test()->subscriber->uuid, $campaignUnsubscribe->subscriber->uuid);
    test()->assertEquals(test()->campaign->uuid, $campaignUnsubscribe->campaign->uuid);

    $subscription = test()->emailList->allSubscribers()->first();
    test()->assertEquals(SubscriptionStatus::UNSUBSCRIBED, $subscription->status);
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

    test()->assertCount(1, CampaignUnsubscribe::all());

    test()->assertStringContainsString('already unsubscribed', $response);
});

test('the unsubscribe will work even if the send is deleted', function () {
    sendCampaign();

    Send::all()->each->delete();

    test()->post(test()->mailedUnsubscribeLink)->assertSuccessful();

    test()->assertEquals(SubscriptionStatus::UNSUBSCRIBED, test()->subscriber->refresh()->status);
});

test('the unsubscribe header is added to the email', function () {
    Event::listen(MessageSent::class, function (MessageSent $event) {
        $subscription = test()->emailList->allSubscribers()->first();

        test()->assertNotNull($event->message->getHeaders()->get('List-Unsubscribe'));

        test()->assertEquals('<'.url(action(UnsubscribeController::class, [$subscription->uuid, Send::first()->uuid])).'>', $event->message->getHeaders()->get('List-Unsubscribe')->getValue());

        test()->assertNotNull($event->message->getHeaders()->get('List-Unsubscribe-Post'));

        test()->assertEquals('List-Unsubscribe=One-Click', $event->message->getHeaders()->get('List-Unsubscribe-Post')->getValue());
    });

    dispatch(new SendCampaignJob(test()->campaign));
});

// Helpers
function sendCampaign()
{
    Event::listen(MessageSent::class, function (MessageSent $event) {
        $link = (new Crawler($event->message->getBody()))
            ->filter('a')->first()->attr('href');

        test()->assertStringStartsWith('http://localhost', $link);

        test()->mailedUnsubscribeLink = Str::after($link, 'http://localhost');
    });

    dispatch(new SendCampaignJob(test()->campaign));
}

<?php

use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Enums\SendFeedbackType;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

uses(TestCase::class);

it('can be found by its transport message id', function () {
    $send = SendFactory::new()->create([
        'transport_message_id' => '1234',
    ]);

    test()->assertTrue($send->is(Send::findByTransportMessageId('1234')));
});

it('will unsubscribe when there is a permanent bounce', function () {
    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
    $subscriber = Subscriber::factory()->create();

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */
    $emailList = $subscriber->emailList;

    $campaign = Campaign::factory()->create([
        'email_list_id' => $emailList->id,
    ]);

    $send = SendFactory::new()->create([
        'campaign_id' => $campaign->id,
        'subscriber_id' => $subscriber->id,
    ]);

    $bouncedAt = now()->subHour();
    $send->registerBounce($bouncedAt);

    test()->assertDatabaseHas('mailcoach_send_feedback_items', [
        'send_id' => $send->id,
        'type' => SendFeedbackType::BOUNCE,
        'created_at' => $bouncedAt,
    ]);

    test()->assertFalse($emailList->isSubscribed($subscriber->email));
});

it('can receive a complaint', function () {
    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
    $subscriber = Subscriber::factory()->create();

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */
    $emailList = $subscriber->emailList;

    $campaign = Campaign::factory()->create([
        'email_list_id' => $emailList->id,
    ]);

    $send = SendFactory::new()->create([
        'campaign_id' => $campaign->id,
        'subscriber_id' => $subscriber->id,
    ]);

    $complainedAt = now()->subHour();
    $send->registerComplaint($complainedAt);

    test()->assertDatabaseHas('mailcoach_send_feedback_items', [
        'send_id' => $send->id,
        'type' => SendFeedbackType::COMPLAINT,
        'created_at' => $complainedAt,
    ]);

    test()->assertFalse($emailList->isSubscribed($subscriber->email));
});

it('will not register an open if it was recently opened', function () {
    TestTime::freeze();

    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update(['track_opens' => true]);

    $send->registerOpen();
    test()->assertCount(1, $send->opens()->get());

    TestTime::addSeconds(4);
    $send->registerOpen();
    test()->assertCount(1, $send->opens()->get());

    TestTime::addSeconds(1);
    $send->registerOpen();
    test()->assertCount(2, $send->opens()->get());
});

it('will register an open at a specific time', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update(['track_opens' => true]);

    $openedAt = now()->subHour()->setMicroseconds(0);

    $send->registerOpen($openedAt);

    test()->assertEquals($openedAt, $send->opens()->first()->created_at);
});

it('will not register a click of an unsubscribe link', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update(['track_clicks' => true]);

    $unsubscribeUrl = $send->subscriber->unsubscribeUrl($send);

    $send->registerClick($unsubscribeUrl);

    test()->assertCount(0, $send->clicks()->get());
});

it('can register a click at a given time', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update(['track_clicks' => true]);

    $clickedAt = now()->subDay()->setMilliseconds(0);
    $send->registerClick('https://example.com', $clickedAt);

    test()->assertCount(1, $send->clicks()->get());
    test()->assertEquals($clickedAt, $send->clicks()->first()->created_at);
});

it('can register a click and strips utm tags', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update(['track_clicks' => true]);

    $clickedAt = now()->subDay()->setMilliseconds(0);
    $send->registerClick('https://example.com?utm_campaign=My+campaign', $clickedAt);

    test()->assertCount(1, $send->clicks()->get());
    test()->assertEquals($clickedAt, $send->clicks()->first()->created_at);
    test()->assertEquals('https://example.com', $send->clicks()->first()->link->url);
});

test('registering clicks will update the click count', function () {
    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
    $subscriber = Subscriber::factory()->create();

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */
    $emailList = $subscriber->emailList;

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $anotherSubscriber */
    $anotherSubscriber = Subscriber::factory()->create(['email_list_id' => $emailList->id]);

    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */
    $campaign = Campaign::factory()->create([
        'email_list_id' => $emailList->id,
        'track_clicks' => true,
    ]);

    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create([
        'campaign_id' => $campaign->id,
        'subscriber_id' => $subscriber->id,
    ]);

    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $anotherSend */
    $anotherSend = SendFactory::new()->create([
        'campaign_id' => $campaign->id,
        'subscriber_id' => $anotherSubscriber->id,
    ]);

    $linkA = 'https://mailcoach.app';
    $linkB = 'https://spatie.be';

    $campaignClick = $send->registerClick($linkA);

    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink $campaignLinkA */
    $campaignLinkA = $campaignClick->link;

    test()->assertEquals(1, $campaignLinkA->click_count);
    test()->assertEquals(1, $campaignLinkA->unique_click_count);

    $send->registerClick($linkA);
    test()->assertEquals(2, $campaignLinkA->refresh()->click_count);
    test()->assertEquals(1, $campaignLinkA->refresh()->unique_click_count);

    $anotherSend->registerClick($linkA);
    test()->assertEquals(3, $campaignLinkA->refresh()->click_count);
    test()->assertEquals(2, $campaignLinkA->refresh()->unique_click_count);

    $anotherSend->registerClick($linkA);
    test()->assertEquals(4, $campaignLinkA->refresh()->click_count);
    test()->assertEquals(2, $campaignLinkA->refresh()->unique_click_count);

    $campaignClick = $send->registerClick($linkB);

    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink $campaignLinkA */
    $campaignLinkB = $campaignClick->link;

    test()->assertEquals(1, $campaignLinkB->click_count);
    test()->assertEquals(1, $campaignLinkB->unique_click_count);

    $send->registerClick($linkB);
    test()->assertEquals(2, $campaignLinkB->refresh()->click_count);
    test()->assertEquals(1, $campaignLinkB->refresh()->unique_click_count);
});

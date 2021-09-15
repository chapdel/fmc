<?php

use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Enums\SendFeedbackType;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\TestTime\TestTime;

it('can be found by its transport message id', function () {
    $send = SendFactory::new()->create([
        'transport_message_id' => '1234',
    ]);

    expect($send->is(Send::findByTransportMessageId('1234')))->toBeTrue();
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

    expect($emailList->isSubscribed($subscriber->email))->toBeFalse();
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

    expect($emailList->isSubscribed($subscriber->email))->toBeFalse();
});

it('will not register an open if it was recently opened', function () {
    TestTime::freeze();

    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update(['track_opens' => true]);

    $send->registerOpen();
    expect($send->opens()->get())->toHaveCount(1);

    TestTime::addSeconds(4);
    $send->registerOpen();
    expect($send->opens()->get())->toHaveCount(1);

    TestTime::addSeconds(1);
    $send->registerOpen();
    expect($send->opens()->get())->toHaveCount(2);
});

it('will register an open at a specific time', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update(['track_opens' => true]);

    $openedAt = now()->subHour()->setMicroseconds(0);

    $send->registerOpen($openedAt);

    expect($send->opens()->first()->created_at)->toEqual($openedAt);
});

it('will not register a click of an unsubscribe link', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update(['track_clicks' => true]);

    $unsubscribeUrl = $send->subscriber->unsubscribeUrl($send);

    $send->registerClick($unsubscribeUrl);

    expect($send->clicks()->get())->toHaveCount(0);
});

it('can register a click at a given time', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update(['track_clicks' => true]);

    $clickedAt = now()->subDay()->setMilliseconds(0);
    $send->registerClick('https://example.com', $clickedAt);

    expect($send->clicks()->get())->toHaveCount(1);
    expect($send->clicks()->first()->created_at)->toEqual($clickedAt);
});

it('can register a click and strips utm tags', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update(['track_clicks' => true]);

    $clickedAt = now()->subDay()->setMilliseconds(0);
    $send->registerClick('https://example.com?utm_campaign=My+campaign', $clickedAt);

    expect($send->clicks()->get())->toHaveCount(1);
    expect($send->clicks()->first()->created_at)->toEqual($clickedAt);
    expect($send->clicks()->first()->link->url)->toEqual('https://example.com');
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

    expect($campaignLinkA->click_count)->toEqual(1);
    expect($campaignLinkA->unique_click_count)->toEqual(1);

    $send->registerClick($linkA);
    expect($campaignLinkA->refresh()->click_count)->toEqual(2);
    expect($campaignLinkA->refresh()->unique_click_count)->toEqual(1);

    $anotherSend->registerClick($linkA);
    expect($campaignLinkA->refresh()->click_count)->toEqual(3);
    expect($campaignLinkA->refresh()->unique_click_count)->toEqual(2);

    $anotherSend->registerClick($linkA);
    expect($campaignLinkA->refresh()->click_count)->toEqual(4);
    expect($campaignLinkA->refresh()->unique_click_count)->toEqual(2);

    $campaignClick = $send->registerClick($linkB);

    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink $campaignLinkA */
    $campaignLinkB = $campaignClick->link;

    expect($campaignLinkB->click_count)->toEqual(1);
    expect($campaignLinkB->unique_click_count)->toEqual(1);

    $send->registerClick($linkB);
    expect($campaignLinkB->refresh()->click_count)->toEqual(2);
    expect($campaignLinkB->refresh()->unique_click_count)->toEqual(1);
});

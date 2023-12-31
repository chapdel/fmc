<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Content\Models\Click;
use Spatie\Mailcoach\Domain\Content\Models\Link;
use Spatie\Mailcoach\Domain\Content\Models\Open;
use Spatie\Mailcoach\Domain\Shared\Enums\SendFeedbackType;
use Spatie\Mailcoach\Domain\Shared\Events\WebhookCallProcessedEvent;
use Spatie\Mailcoach\Domain\Shared\Models\SendFeedbackItem;
use Spatie\Mailcoach\Domain\Vendor\Mailgun\Jobs\ProcessMailgunWebhookJob;
use Spatie\WebhookClient\Models\WebhookCall;

beforeEach(function () {
    $this->webhookCall = WebhookCall::create([
        'name' => 'mailgun',
        'payload' => getMailgunStub('bounceWebhookContent.json'),
    ]);

    $this->send = SendFactory::new()->create([
        'transport_message_id' => '20130503192659.13651.20287@mg.craftremote.com',
    ]);
});

it('processes a mailgun bounce webhook call', function () {
    (new ProcessMailgunWebhookJob($this->webhookCall))->handle();

    expect(SendFeedbackItem::count())->toEqual(1);

    tap(SendFeedbackItem::first(), function (SendFeedbackItem $sendFeedbackItem) {
        expect($sendFeedbackItem->type)->toEqual(SendFeedbackType::Bounce);
        expect($sendFeedbackItem->created_at)->toEqual(Carbon::createFromTimestamp(1521233195));
        expect($this->send->is($sendFeedbackItem->send))->toBeTrue();
    });
});

it('processes a mailgun complaint webhook call', function () {
    $this->webhookCall->update(['payload' => getMailgunStub('complaintWebhookContent.json')]);
    (new ProcessMailgunWebhookJob($this->webhookCall))->handle();

    expect(SendFeedbackItem::count())->toEqual(1);
    tap(SendFeedbackItem::first(), function (SendFeedbackItem $sendFeedbackItem) {
        expect($sendFeedbackItem->type)->toEqual(SendFeedbackType::Complaint);
        expect($sendFeedbackItem->created_at)->toEqual(Carbon::createFromTimestamp(1521233123));
        expect($this->send->is($sendFeedbackItem->send))->toBeTrue();
    });
});

it('processes a mailgun click webhook call', function () {
    $this->webhookCall->update(['payload' => getMailgunStub('clickWebhookContent.json')]);
    (new ProcessMailgunWebhookJob($this->webhookCall))->handle();

    expect(Link::count())->toEqual(1);
    expect(Link::first()->url)->toEqual('http://example.com/signup');
    expect(Link::first()->clicks)->toHaveCount(1);
    tap(Link::first()->clicks->first(), function (Click $campaignClick) {
        expect($campaignClick->created_at)->toEqual(Carbon::createFromTimestamp(1377075564));
    });
});

it('can process a mailgun open webhook call', function () {
    $this->webhookCall->update(['payload' => getMailgunStub('openWebhookContent.json')]);
    (new ProcessMailgunWebhookJob($this->webhookCall))->handle();

    expect($this->send->contentItem->opens)->toHaveCount(1);
    tap($this->send->contentItem->opens->first(), function (Open $open) {
        expect($open->created_at)->toEqual(Carbon::createFromTimestamp(1377047343));
    });
});

it('fires an event after processing the webhook call', function () {
    Event::fake(WebhookCallProcessedEvent::class);

    $this->webhookCall->update(['payload' => getMailgunStub('openWebhookContent.json')]);
    (new ProcessMailgunWebhookJob($this->webhookCall))->handle();

    Event::assertDispatched(WebhookCallProcessedEvent::class);
});

it('will not handle unrelated events', function () {
    $this->webhookCall->update(['payload' => getMailgunStub('otherWebhookContent.json')]);
    (new ProcessMailgunWebhookJob($this->webhookCall))->handle();

    expect(Link::count())->toEqual(0);
    expect(Open::count())->toEqual(0);
    expect(SendFeedbackItem::count())->toEqual(0);
});

it('does nothing when it cannot find the transport message id', function () {
    $data = $this->webhookCall->payload;
    $data['event-data']['message']['headers']['message-id'] = 'some-other-id';

    $this->webhookCall->update([
        'payload' => $data,
    ]);

    $job = new ProcessMailgunWebhookJob($this->webhookCall);

    $job->handle();

    expect(SendFeedbackItem::count())->toEqual(0);
});

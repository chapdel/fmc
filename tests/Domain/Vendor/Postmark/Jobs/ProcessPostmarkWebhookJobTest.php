<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Content\Models\Link;
use Spatie\Mailcoach\Domain\Content\Models\Open;
use Spatie\Mailcoach\Domain\Shared\Enums\SendFeedbackType;
use Spatie\Mailcoach\Domain\Shared\Events\WebhookCallProcessedEvent;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Models\SendFeedbackItem;
use Spatie\Mailcoach\Domain\Vendor\Postmark\Jobs\ProcessPostmarkWebhookJob;
use Spatie\WebhookClient\Models\WebhookCall;

beforeEach(function () {
    $this->webhookCall = WebhookCall::create([
        'name' => 'postmark',
        'payload' => getStub('bounceWebhookContent'),
    ]);

    $this->send = Send::factory()->create();

    $this->send->update(['uuid' => 'my-uuid']);
});

it('processes a postmark bounce webhook call', function () {
    (new ProcessPostmarkWebhookJob($this->webhookCall))->handle();

    expect(SendFeedbackItem::count())->toEqual(1);
    tap(SendFeedbackItem::first(), function (SendFeedbackItem $sendFeedbackItem) {
        expect($sendFeedbackItem->type)->toEqual(SendFeedbackType::Bounce);
        expect($sendFeedbackItem->created_at)->toEqual(Carbon::parse('2019-11-05T16:33:54.0Z'));
        expect($this->send->is($sendFeedbackItem->send))->toBeTrue();
    });
});

it('processes a postmark bounce via subscription change webhook call', function () {
    $this->webhookCall->update(['payload' => getStub('bounceViaSubscriptionChangeWebhookContent')]);
    (new ProcessPostmarkWebhookJob($this->webhookCall))->handle();

    expect(SendFeedbackItem::count())->toEqual(1);
    tap(SendFeedbackItem::first(), function (SendFeedbackItem $sendFeedbackItem) {
        expect($sendFeedbackItem->type)->toEqual(SendFeedbackType::Bounce);
        expect($sendFeedbackItem->created_at)->toEqual(Carbon::parse('2019-11-05T16:33:54.0Z'));
        expect($this->send->is($sendFeedbackItem->send))->toBeTrue();
    });
});

it('wil not process a postmark soft bounce webhook call', function () {
    $this->webhookCall->update(['payload' => getStub('softBounceWebhookContent')]);
    (new ProcessPostmarkWebhookJob($this->webhookCall))->handle();

    expect(SendFeedbackItem::count())->toEqual(0);
});

it('processes a postmark complaint webhook call', function () {
    $this->webhookCall->update(['payload' => getStub('complaintWebhookContent')]);
    (new ProcessPostmarkWebhookJob($this->webhookCall))->handle();

    expect(SendFeedbackItem::count())->toEqual(1);
    tap(SendFeedbackItem::first(), function (SendFeedbackItem $sendFeedbackItem) {
        expect($sendFeedbackItem->type)->toEqual(SendFeedbackType::Complaint);
        expect($sendFeedbackItem->created_at)->toEqual(Carbon::parse('2019-11-05T16:33:54.0Z'));
        expect($this->send->is($sendFeedbackItem->send))->toBeTrue();
    });
});

it('processes a postmark complaint via subscription change webhook call', function () {
    $this->webhookCall->update(['payload' => getStub('complaintViaSubscriptionChangeWebhookContent')]);
    (new ProcessPostmarkWebhookJob($this->webhookCall))->handle();

    expect(SendFeedbackItem::count())->toEqual(1);
    tap(SendFeedbackItem::first(), function (SendFeedbackItem $sendFeedbackItem) {
        expect($sendFeedbackItem->type)->toEqual(SendFeedbackType::Complaint);
        expect($sendFeedbackItem->created_at)->toEqual(Carbon::parse('2019-11-05T16:33:54.0Z'));
        expect($this->send->is($sendFeedbackItem->send))->toBeTrue();
    });
});

it('processes a postmark click webhook call', function () {
    $this->webhookCall->update(['payload' => getStub('clickWebhookContent')]);
    (new ProcessPostmarkWebhookJob($this->webhookCall))->handle();

    expect(Link::count())->toEqual(1);
    expect(Link::first()->url)->toEqual('http://example.com/signup');
    expect(Link::first()->clicks)->toHaveCount(1);
    expect(Link::first()->clicks->first()->created_at)->toEqual(Carbon::parse('2017-10-25T15:21:11.0Z'));
});

it('can process a postmark open webhook call', function () {
    $this->webhookCall->update(['payload' => getStub('openWebhookContent')]);
    (new ProcessPostmarkWebhookJob($this->webhookCall))->handle();

    expect($this->send->campaign->opens)->toHaveCount(1);
    expect($this->send->campaign->opens->first()->created_at)->toEqual(Carbon::parse('2019-11-05T16:33:54.0Z'));
});

it('can process a postmark open webhook call by message id', function () {
    $this->send->update(['transport_message_id' => 'some-message-id']);
    $payload = getStub('openWebhookContent');
    $payload['MessageID'] = 'some-message-id';
    unset($payload['Metadata']);

    $this->webhookCall->update(['payload' => $payload]);
    (new ProcessPostmarkWebhookJob($this->webhookCall))->handle();

    expect($this->send->campaign->opens)->toHaveCount(1);
    expect($this->send->campaign->opens->first()->created_at)->toEqual(Carbon::parse('2019-11-05T16:33:54.0Z'));
});

it('fires an event after processing the webhook call', function () {
    Event::fake(WebhookCallProcessedEvent::class);

    $this->webhookCall->update(['payload' => getStub('openWebhookContent')]);
    (new ProcessPostmarkWebhookJob($this->webhookCall))->handle();

    Event::assertDispatched(WebhookCallProcessedEvent::class);
});

it('will not handle unrelated events', function () {
    $this->webhookCall->update(['payload' => getStub('otherWebhookContent')]);
    (new ProcessPostmarkWebhookJob($this->webhookCall))->handle();

    expect(Link::count())->toEqual(0);
    expect(Open::count())->toEqual(0);
    expect(SendFeedbackItem::count())->toEqual(0);
});

it('does nothing when it cannot find a send for the uuid in the webhook', function () {
    $data = $this->webhookCall->payload;
    $data['Metadata']['send-uuid'] = 'some-other-uuid';

    $this->webhookCall->update([
        'payload' => $data,
    ]);

    $job = new ProcessPostmarkWebhookJob($this->webhookCall);

    $job->handle();

    expect(SendFeedbackItem::count())->toEqual(0);
});

it('will not fail if  record type is not set', function () {
    $payload = getStub('clickWebhookContent');

    unset($payload['RecordType']);

    $this->webhookCall->update(['payload' => $payload]);
    (new ProcessPostmarkWebhookJob($this->webhookCall))->handle();

    expect(Link::count())->toEqual(0);
});

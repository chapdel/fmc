<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Content\Models\Link;
use Spatie\Mailcoach\Domain\Content\Models\Open;
use Spatie\Mailcoach\Domain\Shared\Enums\SendFeedbackType;
use Spatie\Mailcoach\Domain\Shared\Events\WebhookCallProcessedEvent;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Models\SendFeedbackItem;
use Spatie\Mailcoach\Domain\Vendor\Sendgrid\Jobs\ProcessSendgridWebhookJob;
use Spatie\WebhookClient\Models\WebhookCall;

beforeEach(function () {
    $this->webhookCall = WebhookCall::create([
        'name' => 'sendgrid',
        'payload' => getSendgridStub('multipleEventsPayload.json'),
    ]);

    $this->send = Send::factory()->create();
    $this->send->update(['uuid' => 'test-uuid']);
    $this->send->subscriber->update(['email' => 'example@test.com']);
});

it('can handle multiple events', function () {
    (new ProcessSendgridWebhookJob($this->webhookCall))->handle();

    expect(SendFeedbackItem::count())->toEqual(2);
    expect(SendFeedbackItem::first()->type)->toEqual(SendFeedbackType::Bounce);
    expect($this->send->is(SendFeedbackItem::first()->send))->toBeTrue();
});

it('processes a sendgrid complaint webhook call', function () {
    $this->webhookCall->update(['payload' => getSendgridStub('complaintPayload.json')]);
    (new ProcessSendgridWebhookJob($this->webhookCall))->handle();

    expect(SendFeedbackItem::count())->toEqual(1);
    tap(SendFeedbackItem::first(), function (SendFeedbackItem $sendFeedbackItem) {
        expect($sendFeedbackItem->type)->toEqual(SendFeedbackType::Complaint);
        expect($sendFeedbackItem->created_at)->toEqual(Carbon::createFromTimestamp(1574854444));
        expect($this->send->is($sendFeedbackItem->send))->toBeTrue();
    });

    $this->send->subscriber->update(['email' => 'not-example@test.com']);
    (new ProcessSendgridWebhookJob($this->webhookCall))->handle();
    expect(SendFeedbackItem::count())->toEqual(1);
});

it('processes a sendgrid click webhook call', function () {
    $this->webhookCall->update(['payload' => getSendgridStub('clickPayload.json')]);
    (new ProcessSendgridWebhookJob($this->webhookCall))->handle();

    expect(Link::count())->toEqual(1);
    expect(Link::first()->url)->toEqual('https://example.com');
    expect(Link::first()->clicks)->toHaveCount(1);
    expect(Link::first()->clicks->first()->created_at)->toEqual(Carbon::createFromTimestamp(1574854444));

    $this->send->subscriber->update(['email' => 'not-example@test.com']);
    (new ProcessSendgridWebhookJob($this->webhookCall))->handle();
    expect(Link::count())->toEqual(1);
    expect(Link::first()->clicks)->toHaveCount(1);
});

it('processes a sendgrid click webhook call with message id', function () {
    $this->send->update(['transport_message_id' => '14c5d75ce93']);

    $payload = getSendgridStub('clickPayload.json');
    unset($payload['send_uuid']);

    $this->webhookCall->update(['payload' => $payload]);
    (new ProcessSendgridWebhookJob($this->webhookCall))->handle();

    expect(Link::count())->toEqual(1);
    expect(Link::first()->url)->toEqual('https://example.com');
    expect(Link::first()->clicks)->toHaveCount(1);
    expect(Link::first()->clicks->first()->created_at)->toEqual(Carbon::createFromTimestamp(1574854444));

    $this->send->subscriber->update(['email' => 'not-example@test.com']);
    (new ProcessSendgridWebhookJob($this->webhookCall))->handle();
    expect(Link::count())->toEqual(1);
    expect(Link::first()->clicks)->toHaveCount(1);
});

it('can process a sendgrid open webhook call', function () {
    $this->webhookCall->update(['payload' => getSendgridStub('openPayload.json')]);
    (new ProcessSendgridWebhookJob($this->webhookCall))->handle();

    expect($this->send->campaign->opens)->toHaveCount(1);
    expect($this->send->campaign->opens->first()->created_at)->toEqual(Carbon::createFromTimestamp(1574854444));

    $this->send->subscriber->update(['email' => 'not-example@test.com']);
    (new ProcessSendgridWebhookJob($this->webhookCall))->handle();
    expect($this->send->campaign->fresh()->opens)->toHaveCount(1);
});

it('can process a sendgrid bounce webhook call', function () {
    $this->webhookCall->update(['payload' => getSendgridStub('bouncePayload.json')]);
    (new ProcessSendgridWebhookJob($this->webhookCall))->handle();

    expect(SendFeedbackItem::count())->toEqual(1);
    expect(SendFeedbackItem::first()->type)->toEqual(SendFeedbackType::Bounce);
    expect($this->send->is(SendFeedbackItem::first()->send))->toBeTrue();
});

it('wont process a sendgrid temporary bounce webhook call', function () {
    $payload = getSendgridStub('bouncePayload.json');
    $payload[0]['type'] = 'blocked';

    $this->webhookCall->update(['payload' => $payload]);
    (new ProcessSendgridWebhookJob($this->webhookCall))->handle();

    expect(SendFeedbackItem::count())->toEqual(0);
});

it('will fire an event when processing is complete', function () {
    Event::fake(WebhookCallProcessedEvent::class);

    $this->webhookCall->update(['payload' => getSendgridStub('openPayload.json')]);
    (new ProcessSendgridWebhookJob($this->webhookCall))->handle();

    Event::assertDispatched(WebhookCallProcessedEvent::class);
});

it('will not handle unrelated events', function () {
    $this->webhookCall->update(['payload' => getSendgridStub('otherPayload.json')]);
    (new ProcessSendgridWebhookJob($this->webhookCall))->handle();

    expect(Link::count())->toEqual(0);
    expect(Link::count())->toEqual(0);
    expect(SendFeedbackItem::count())->toEqual(0);
});

it('does nothing when it cannot find the transport message id', function () {
    $data = $this->webhookCall->payload;
    $data[0]['send_uuid'] = 'some-other-uuid';
    $data[1]['send_uuid'] = 'some-other-uuid';

    $this->webhookCall->update([
        'payload' => $data,
    ]);

    $job = new ProcessSendgridWebhookJob($this->webhookCall);

    $job->handle();

    expect(SendFeedbackItem::count())->toEqual(0);
});

it('will not handle events without send uuid', function () {
    $this->webhookCall->update(['payload' => getSendgridStub('noSendUuidPayload.json')]);
    (new ProcessSendgridWebhookJob($this->webhookCall))->handle();

    expect(Link::count())->toEqual(0);
    expect(Open::count())->toEqual(0);
    expect(SendFeedbackItem::count())->toEqual(0);
});

it('wont handle the same event ids twice', function () {
    $call2 = WebhookCall::create([
        'name' => 'sendgrid',
        'payload' => getSendgridStub('multipleEventsPayload.json'),
    ]);

    $job = new ProcessSendgridWebhookJob($this->webhookCall);
    $job->handle();

    $job = new ProcessSendgridWebhookJob($call2);
    $job->handle();

    expect(SendFeedbackItem::count())->toEqual(2);

    expect($call2->fresh()->payload)->toEqual([]);
});

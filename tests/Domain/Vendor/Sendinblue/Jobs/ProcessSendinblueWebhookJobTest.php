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
use Spatie\Mailcoach\Domain\Vendor\Sendinblue\Jobs\ProcessSendinblueWebhookJob;
use Spatie\WebhookClient\Models\WebhookCall;

beforeEach(function () {
    $this->webhookCall = WebhookCall::create([
        'name' => 'sendinblue',
        'payload' => getSendinblueStub('bounceWebhookContent.json'),
    ]);

    $this->send = SendFactory::new()->create([
        'transport_message_id' => 'xxx@msgid.domain',
    ]);
});

it('processes a sendinblue bounce webhook call', function () {
    (new ProcessSendinblueWebhookJob($this->webhookCall))->handle();

    expect(SendFeedbackItem::count())->toEqual(1);

    tap(SendFeedbackItem::first(), function (SendFeedbackItem $sendFeedbackItem) {
        expect($sendFeedbackItem->type)->toEqual(SendFeedbackType::Bounce);
        expect($sendFeedbackItem->created_at)->toEqual(Carbon::createFromTimestampMs(1534486682000));
        expect($this->send->is($sendFeedbackItem->send))->toBeTrue();
    });
});

it('processes a sendinblue complaint webhook call', function () {
    $this->webhookCall->update(['payload' => getSendinblueStub('complaintWebhookContent.json')]);
    (new ProcessSendinblueWebhookJob($this->webhookCall))->handle();

    expect(SendFeedbackItem::count())->toEqual(1);
    tap(SendFeedbackItem::first(), function (SendFeedbackItem $sendFeedbackItem) {
        expect($sendFeedbackItem->type)->toEqual(SendFeedbackType::Complaint);
        expect($sendFeedbackItem->created_at)->toEqual(now()->startOfSecond());
        expect($this->send->is($sendFeedbackItem->send))->toBeTrue();
    });
});

it('processes a sendinblue click webhook call', function () {
    $this->webhookCall->update(['payload' => getSendinblueStub('clickWebhookContent.json')]);
    (new ProcessSendinblueWebhookJob($this->webhookCall))->handle();

    expect(Link::count())->toEqual(1);
    expect(Link::first()->url)->toEqual('https://www.some-link.com');
    expect(Link::first()->clicks)->toHaveCount(1);
    tap(Link::first()->clicks->first(), function (Click $campaignClick) {
        expect($campaignClick->created_at)->toEqual(Carbon::createFromTimestampMs(1534486682000));
    });
});

it('can process a sendinblue open webhook call', function () {
    $this->webhookCall->update(['payload' => getSendinblueStub('openWebhookContent.json')]);
    (new ProcessSendinblueWebhookJob($this->webhookCall))->handle();

    expect($this->send->campaign->opens)->toHaveCount(1);
    tap($this->send->campaign->opens->first(), function (Open $campaignOpen) {
        expect($campaignOpen->created_at)->toEqual(Carbon::createFromTimestampMs(1534486682000));
    });
});

it('fires an event after processing the webhook call', function () {
    Event::fake(WebhookCallProcessedEvent::class);

    $this->webhookCall->update(['payload' => getSendinblueStub('openWebhookContent.json')]);
    (new ProcessSendinblueWebhookJob($this->webhookCall))->handle();

    Event::assertDispatched(WebhookCallProcessedEvent::class);
});

it('will not handle unrelated events', function () {
    $this->webhookCall->update(['payload' => getSendinblueStub('otherWebhookContent.json')]);
    (new ProcessSendinblueWebhookJob($this->webhookCall))->handle();

    expect(Link::count())->toEqual(0);
    expect(Open::count())->toEqual(0);
    expect(SendFeedbackItem::count())->toEqual(0);
});

it('does nothing when it cannot find the transport message id', function () {
    $data = $this->webhookCall->payload;
    $data['message-id'] = 'some-other-id';

    $this->webhookCall->update([
        'payload' => $data,
    ]);

    $job = new ProcessSendinblueWebhookJob($this->webhookCall);

    $job->handle();

    expect(SendFeedbackItem::count())->toEqual(0);
});

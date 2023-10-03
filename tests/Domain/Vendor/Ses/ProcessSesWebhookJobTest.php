<?php

use Aws\Sns\MessageValidator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Shared\Enums\SendFeedbackType;
use Spatie\Mailcoach\Domain\Shared\Events\WebhookCallProcessedEvent;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Models\SendFeedbackItem;
use Spatie\Mailcoach\Domain\Vendor\Ses\Jobs\ProcessSesWebhookJob;
use Spatie\Mailcoach\Domain\Vendor\Ses\SesWebhookCall;

beforeEach(function () {
    $this->webhookCall = SesWebhookCall::create([
        'name' => 'ses',
        'external_id' => getSesStub('bounceWebhookContent.json')['MessageId'],
        'payload' => getSesStub('bounceWebhookContent.json'),
    ]);

    $this->send = Send::factory()->create([
        'transport_message_id' => '93ef47baa0e7818557569e92494f4be1@swift.generated',
    ]);

    $this->mock(MessageValidator::class)->shouldReceive('isValid')->andReturnTrue();
});

it('does nothing and deletes the call if signature is missing', function () {
    $data = getSesStub('bounceWebhookContent.json');
    $data['Signature'] = null;

    $this->webhookCall->update([
        'payload' => json_encode($data),
    ]);

    $job = new ProcessSesWebhookJob($this->webhookCall);

    $job->handle();
    expect(SendFeedbackItem::count())->toEqual(0);
    expect(SesWebhookCall::count())->toEqual(0);
});

it('does nothing if data is missing', function () {
    $data = getSesStub('bounceWebhookContent.json');
    $data['Message'] = '';

    $this->webhookCall->update([
        'payload' => json_encode($data),
    ]);

    $job = new ProcessSesWebhookJob($this->webhookCall);

    $job->handle();
    expect(SendFeedbackItem::count())->toEqual(0);
    expect(SesWebhookCall::count())->toEqual(0);
});

it('processes a ses webhook call for a bounce', function () {
    $data = getSesStub('bounceWebhookContent.json');

    $this->webhookCall->update([
        'payload' => $data,
    ]);

    $job = new ProcessSesWebhookJob($this->webhookCall);

    $job->handle();

    expect(SendFeedbackItem::count())->toEqual(1);
    tap(SendFeedbackItem::first(), function (SendFeedbackItem $sendFeedbackItem) {
        expect($this->send->is($sendFeedbackItem->send))->toBeTrue();
        expect($sendFeedbackItem->type)->toEqual(SendFeedbackType::Bounce);
        expect($sendFeedbackItem->created_at)->toEqual(Carbon::parse('2019-11-28T09:43:55'));
    });
});

it('processes a ses webhook call for clicks', function () {
    $webhookCall = SesWebhookCall::create([
        'name' => 'ses',
        'external_id' => getSesStub('clickWebhookContent.json')['MessageId'],
        'payload' => getSesStub('clickWebhookContent.json'),
    ]);

    /** @var Send $send */
    $send = Send::factory()->create([
        'transport_message_id' => '441daaa28872991703a3b02a72408c62@swift.generated',
    ]);

    (new ProcessSesWebhookJob($webhookCall))->handle();

    expect($send->clicks->count())->toEqual(1);
});

it('processes a ses webhook call for opens', function () {
    $webhookCall = SesWebhookCall::create([
        'name' => 'ses',
        'external_id' => getSesStub('openWebhookContent.json')['MessageId'],
        'payload' => getSesStub('openWebhookContent.json'),
    ]);

    /** @var Send $send */
    $send = Send::factory()->create([
        'transport_message_id' => '0107018023eb0291-0bc7253b-53c2-473f-8efd-88e3637c18ce-000000',
    ]);

    (new ProcessSesWebhookJob($webhookCall))->handle();

    expect($send->opens->count())->toEqual(1);
});

it('processes a ses webhook call for opens with message id from header', function () {
    $webhookCall = SesWebhookCall::create([
        'name' => 'ses',
        'external_id' => getSesStub('openWebhookContent.json')['MessageId'],
        'payload' => getSesStub('openWebhookContent.json'),
    ]);

    /** @var Send $send */
    $send = Send::factory()->create([
        'transport_message_id' => 'ebe712eb83fab12b595b69657d2bfe55@spatie.be',
    ]);

    (new ProcessSesWebhookJob($webhookCall))->handle();

    expect($send->opens->count())->toEqual(1);
});

it('processes a ses webhook call for complaints', function () {
    $webhookCall = SesWebhookCall::create([
        'name' => 'ses',
        'external_id' => getSesStub('complaintWebhookContent.json')['MessageId'],
        'payload' => getSesStub('complaintWebhookContent.json'),
    ]);

    /** @var Send $send */
    $send = Send::factory()->create([
        'transport_message_id' => '5d5929d61c2bfd8de65f2cf07a1457de@swift.generated',
    ]);

    (new ProcessSesWebhookJob($webhookCall))->handle();

    expect(SendFeedbackItem::count())->toEqual(1);
    tap(SendFeedbackItem::first(), function (SendFeedbackItem $sendFeedbackItem) use ($send) {
        expect($send->is($sendFeedbackItem->send))->toBeTrue();
        expect($sendFeedbackItem->type)->toEqual(SendFeedbackType::Complaint);
        expect($sendFeedbackItem->created_at)->toEqual(Carbon::parse('2019-11-28T09:13:57'));
    });
});

it('fires an event when the webhook is processed', function () {
    $webhookCall = SesWebhookCall::create([
        'name' => 'ses',
        'external_id' => getSesStub('clickWebhookContent.json')['MessageId'],
        'payload' => getSesStub('clickWebhookContent.json'),
    ]);

    /** @var Send $send */
    $send = Send::factory()->create([
        'transport_message_id' => 'e56a471288e8874bb27a92b7634ef86f@swift.generated',
    ]);

    Event::fake();

    (new ProcessSesWebhookJob($webhookCall))->handle();

    Event::assertDispatched(WebhookCallProcessedEvent::class);
});

it('does nothing when it cannot find the transport message id', function () {
    $data = $this->webhookCall->payload;
    $message = json_decode($data['Message'], true);
    $this->send->update(['transport_message_id' => 'some-other-id']);
    $data['Message'] = json_encode($message);

    $this->webhookCall->update([
        'payload' => $data,
    ]);

    $job = new ProcessSesWebhookJob($this->webhookCall);

    $job->handle();

    expect(SendFeedbackItem::count())->toEqual(0);
});

it('does nothing and deletes the call fi it\'s a duplicated ses message_id', function () {
    $webhookCallSecond = SesWebhookCall::create([
        'name' => 'ses',
        'external_id' => getSesStub('bounceWebhookContent.json')['MessageId'],
        'payload' => getSesStub('bounceWebhookContent.json'),
    ]);

    (new ProcessSesWebhookJob($this->webhookCall))->handle();
    (new ProcessSesWebhookJob($webhookCallSecond))->handle();

    expect(SendFeedbackItem::count())->toEqual(1);
    expect(SesWebhookCall::count())->toEqual(1);
});

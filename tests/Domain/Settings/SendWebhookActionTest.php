<?php

use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Events\SubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Settings\Actions\SendWebhookAction;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Mailcoach;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\WebhookServer\CallWebhookJob;

beforeEach(function() {
    Queue::fake();

    $this->subscriber = SubscriberFactory::new()->create();

    $this->webhookConfiguration = WebhookConfiguration::factory()->create();
});

it('will send a webhook to a configuration that is used for all lists', function() {
    $this->webhookConfiguration->update(['use_for_all_lists' => true]);

    sendWebhook($this->subscriber);

    Queue::assertPushed(CallWebhookJob::class);
});

it('will not send a webhook to a configuration that is not used for all lists', function() {
    $this->webhookConfiguration->update(['use_for_all_lists' => false]);
    sendWebhook($this->subscriber);

    Queue::assertNothingPushed();
});

it('will not send a webhook to a configuration that accepts webhooks for a specific list', function() {
    $this->webhookConfiguration->update(['use_for_all_lists' => false]);
    $this->webhookConfiguration->emailLists()->attach($this->subscriber->emailList);

    sendWebhook($this->subscriber);

    Queue::assertPushed(CallWebhookJob::class);
});

function sendWebhook(Subscriber $subscriber): void
{
    /** @var SendWebhookAction $sendWebhook */
    $sendWebhook = Mailcoach::getSharedActionClass('send_webhook', SendWebhookAction::class);

    $sendWebhook->execute(
        $subscriber->emailList,
        ['my payload'],
        new SubscribedEvent($subscriber),
    );
}

<?php

use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Events\SubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagAddedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagRemovedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnsubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Settings\Actions\SendWebhookAction;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Mailcoach;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\WebhookServer\CallWebhookJob;

beforeEach(function () {
    Queue::fake();

    $this->subscriber = SubscriberFactory::new()->create();

    $this->webhookConfiguration = WebhookConfiguration::factory()->create([
        'use_for_all_lists' => true,
    ]);
});

it('will send a webhook to a configuration that is used for all lists', function () {
    $this->webhookConfiguration->update(['use_for_all_lists' => true]);

    sendWebhook($this->subscriber);

    Queue::assertPushed(CallWebhookJob::class);
});

it('will not send a webhook to a configuration that is not used for all lists', function () {
    $this->webhookConfiguration->update(['use_for_all_lists' => false]);
    sendWebhook($this->subscriber);

    Queue::assertNothingPushed();
});

it('will not send a webhook to a configuration that accepts webhooks for a specific list', function () {
    $this->webhookConfiguration->update(['use_for_all_lists' => false]);
    $this->webhookConfiguration->emailLists()->attach($this->subscriber->emailList);

    sendWebhook($this->subscriber);

    Queue::assertPushed(CallWebhookJob::class);
});

it('will send a webhook when a user subscribed', function () {
    event(new SubscribedEvent($this->subscriber));

    Queue::assertPushed(CallWebhookJob::class, function (CallWebhookJob $event) {
        expect($event->payload['event'])->toBe('SubscribedEvent');
        expect($event->webhookUrl)->toBe($this->subscriber->emailList->webhookConfigurations()->first()->url);

        return true;
    });
});

it('will send a webhook when a user unsubscribed', function () {
    event(new UnsubscribedEvent($this->subscriber));

    Queue::assertPushed(CallWebhookJob::class, function (CallWebhookJob $event) {
        expect($event->payload['event'])->toBe('UnsubscribedEvent');
        expect($event->webhookUrl)->toBe($this->subscriber->emailList->webhookConfigurations()->first()->url);

        return true;
    });
});

it('will send a webhook when a campaign is sent', function () {
    $campaign = Campaign::factory()->create();

    event(new CampaignSentEvent($campaign));

    Queue::assertPushed(CallWebhookJob::class, function (CallWebhookJob $event) {
        expect($event->payload['event'])->toBe('CampaignSentEvent');
        expect($event->webhookUrl)->toBe($this->subscriber->emailList->webhookConfigurations()->first()->url);

        return true;
    });
});

it('will send a webhook when a tag is added to a subscriber', function () {
    $tag = Tag::factory()->create();

    event(new TagAddedEvent($this->subscriber, $tag));

    Queue::assertPushed(CallWebhookJob::class, function (CallWebhookJob $event) use ($tag) {
        expect($event->payload['event'])->toBe('TagAddedEvent');
        expect($event->payload['added_tag'])->toBe($tag->name);
        expect($event->webhookUrl)->toBe($this->subscriber->emailList->webhookConfigurations()->first()->url);

        return true;
    });
});

it('will send a webhook when a tag is removed from a subscriber', function () {
    $tag = Tag::factory()->create();

    event(new TagRemovedEvent($this->subscriber, $tag));

    Queue::assertPushed(CallWebhookJob::class, function (CallWebhookJob $event) use ($tag) {
        expect($event->payload['event'])->toBe('TagRemovedEvent');
        expect($event->payload['removed_tag'])->toBe($tag->name);
        expect($event->webhookUrl)->toBe($this->subscriber->emailList->webhookConfigurations()->first()->url);

        return true;
    });
});

it('should only send a webhook for events that are enabled', function () {
    config()->set('mailcoach.webhooks.selectable_event_types_enabled', true);

    $this->webhookConfiguration->update([
        'use_for_all_events' => false,
        'events' => ['TagRemovedEvent'],
    ]);

    event(new SubscribedEvent($this->subscriber));
    Queue::assertNotPushed(CallWebhookJob::class);

    event(new TagRemovedEvent($this->subscriber, Tag::factory()->create()));
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

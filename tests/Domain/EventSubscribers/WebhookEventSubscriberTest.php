<?php

use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Events\SubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagAddedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagRemovedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnconfirmedSubscriberCreatedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnsubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\WebhookServer\CallWebhookJob;

beforeEach(function () {
    Queue::fake();

    $this->subscriber = SubscriberFactory::new()->create();

    $this->webhookConfiguration = WebhookConfiguration::factory()->create();
});

it('will send a webhook when someone subscribes', function () {
    event(new SubscribedEvent($this->subscriber));

    Queue::assertPushed(CallWebhookJob::class);
});

it('will send a webhook when an unconfirmed subscriber is created', function () {
    event(new UnconfirmedSubscriberCreatedEvent($this->subscriber));

    Queue::assertPushed(CallWebhookJob::class);
});

it('will send a webhook when someone unsubscribes', function () {
    event(new UnsubscribedEvent($this->subscriber));

    Queue::assertPushed(CallWebhookJob::class);
});

it('will send a webhook when a campaign is sent', function () {
    $campaign = Campaign::factory()->create();

    event(new CampaignSentEvent($campaign));

    Queue::assertPushed(CallWebhookJob::class);
});

it('will send a webhook when a tag is added to a subscriber', function () {
    $tag = Tag::factory()->create();

    event(new TagAddedEvent($this->subscriber, $tag));

    Queue::assertPushed(CallWebhookJob::class);
});

it('will send a webhook when a tag is removed from a subscriber', function () {
    $tag = Tag::factory()->create();

    event(new TagRemovedEvent($this->subscriber, $tag));

    Queue::assertPushed(CallWebhookJob::class);
});

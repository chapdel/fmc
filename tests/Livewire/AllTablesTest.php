<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Content\Models\Link;
use Spatie\Mailcoach\Livewire\Audience\ListsComponent;
use Spatie\Mailcoach\Livewire\Audience\SegmentsComponent;
use Spatie\Mailcoach\Livewire\Audience\SegmentSubscribersComponent;
use Spatie\Mailcoach\Livewire\Audience\SubscriberExportsComponent;
use Spatie\Mailcoach\Livewire\Audience\SubscriberImportsComponent;
use Spatie\Mailcoach\Livewire\Audience\SubscribersComponent;
use Spatie\Mailcoach\Livewire\Audience\SubscriberSendsComponent;
use Spatie\Mailcoach\Livewire\Audience\TagsComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationMailsComponent;
use Spatie\Mailcoach\Livewire\Automations\AutomationsComponent;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignsComponent;
use Spatie\Mailcoach\Livewire\Mailers\MailersComponent;
use Spatie\Mailcoach\Livewire\Templates\TemplatesComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailLogItemsComponent;
use Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailsComponent;
use Spatie\Mailcoach\Livewire\Webhooks\WebhooksComponent;

test('all tables render', function ($class) {
    $this->authenticate();

    Livewire::test($class, [
        'automationMail' => AutomationMail::factory()->has(ContentItem::factory())->create(),
        'campaign' => Campaign::factory()->has(ContentItem::factory())->create(),
        'link' => Link::factory()->create(),
        'emailList' => EmailList::factory()->create(),
        'segment' => TagSegment::factory()->create(),
        'subscriber' => Subscriber::factory()->create(),
    ])->assertSuccessful();
})->with([
    'ListsComponent' => ListsComponent::class,
    'SegmentsComponent' => SegmentsComponent::class,
    'SegmentSubscribersComponent' => SegmentSubscribersComponent::class,
    'SubscribersComponent' => SubscribersComponent::class,
    'SubscriberSendsComponent' => SubscriberSendsComponent::class,
    'SubscriberImportsComponent' => SubscriberImportsComponent::class,
    'SubscriberExportsComponent' => SubscriberExportsComponent::class,
    'TagsComponent' => TagsComponent::class,
    'AutomationsComponent' => AutomationsComponent::class,
    'AutomationMailsComponent' => AutomationMailsComponent::class,
    'CampaignsComponent' => CampaignsComponent::class,
    'TemplatesComponent' => TemplatesComponent::class,
    'TransactionalMailLogItemsComponent' => TransactionalMailLogItemsComponent::class,
    'TransactionalMailsComponent' => TransactionalMailsComponent::class,
    'MailersComponent' => MailersComponent::class,
    'WebhooksComponent' => WebhooksComponent::class,
]);

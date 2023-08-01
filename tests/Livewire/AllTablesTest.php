<?php

use Livewire\Livewire;

test('all tables render', function ($class) {
    $this->authenticate();

    Livewire::test($class, [
        'automationMail' => \Spatie\Mailcoach\Domain\Automation\Models\AutomationMail::factory()->create(),
        'automationMailLink' => \Spatie\Mailcoach\Domain\Automation\Models\AutomationMailLink::factory()->create(),
        'campaign' => \Spatie\Mailcoach\Domain\Campaign\Models\Campaign::factory()->create(),
        'campaignLink' => \Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink::factory()->create(),
        'emailList' => \Spatie\Mailcoach\Domain\Audience\Models\EmailList::factory()->create(),
        'segment' => \Spatie\Mailcoach\Domain\Audience\Models\TagSegment::factory()->create(),
        'subscriber' => \Spatie\Mailcoach\Domain\Audience\Models\Subscriber::factory()->create(),
    ])->assertSuccessful();
})->with([
    'ListsComponent' => \Spatie\Mailcoach\Livewire\Audience\ListsComponent::class,
    'SegmentsComponent' => \Spatie\Mailcoach\Livewire\Audience\SegmentsComponent::class,
    'SegmentSubscribersComponent' => \Spatie\Mailcoach\Livewire\Audience\SegmentSubscribersComponent::class,
    'SubscribersComponent' => \Spatie\Mailcoach\Livewire\Audience\SubscribersComponent::class,
    'SubscriberSendsComponent' => \Spatie\Mailcoach\Livewire\Audience\SubscriberSendsComponent::class,
    'SubscriberImportsComponent' => \Spatie\Mailcoach\Livewire\Audience\SubscriberImportsComponent::class,
    'TagsComponent' => \Spatie\Mailcoach\Livewire\Audience\TagsComponent::class,
    'AutomationsComponent' => \Spatie\Mailcoach\Livewire\Automations\AutomationsComponent::class,
    'AutomationMailsComponent' => \Spatie\Mailcoach\Livewire\Automations\AutomationMailsComponent::class,
    'AutomationMailClicksComponent' => \Spatie\Mailcoach\Livewire\Automations\AutomationMailClicksComponent::class,
    'AutomationMailOpensComponent' => \Spatie\Mailcoach\Livewire\Automations\AutomationMailOpensComponent::class,
    'AutomationMailUnsubscribesComponent' => \Spatie\Mailcoach\Livewire\Automations\AutomationMailUnsubscribesComponent::class,
    'AutomationMailOutboxComponent' => \Spatie\Mailcoach\Livewire\Automations\AutomationMailOutboxComponent::class,
    'CampaignsComponent' => \Spatie\Mailcoach\Livewire\Campaigns\CampaignsComponent::class,
    'TemplatesComponent' => \Spatie\Mailcoach\Livewire\Templates\TemplatesComponent::class,
    'CampaignClicksComponent' => \Spatie\Mailcoach\Livewire\Campaigns\CampaignClicksComponent::class,
    'CampaignLinkClicksComponent' => \Spatie\Mailcoach\Livewire\Campaigns\CampaignLinkClicksComponent::class,
    'CampaignOpensComponent' => \Spatie\Mailcoach\Livewire\Campaigns\CampaignOpensComponent::class,
    'CampaignUnsubscribesComponent' => \Spatie\Mailcoach\Livewire\Campaigns\CampaignUnsubscribesComponent::class,
    'CampaignOutboxComponent' => \Spatie\Mailcoach\Livewire\Campaigns\CampaignOutboxComponent::class,
    'TransactionalMailLogItemsComponent' => \Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailLogItemsComponent::class,
    'TransactionalMailsComponent' => \Spatie\Mailcoach\Livewire\TransactionalMails\TransactionalMailsComponent::class,
    'MailersComponent' => \Spatie\Mailcoach\Livewire\Mailers\MailersComponent::class,
    'UsersComponent' => \Spatie\Mailcoach\Livewire\Users\UsersComponent::class,
    'AutomationMailLinkClicksComponent' => \Spatie\Mailcoach\Livewire\Automations\AutomationMailLinkClicksComponent::class,
    'WebhooksComponent' => \Spatie\Mailcoach\Livewire\Webhooks\WebhooksComponent::class,
]);

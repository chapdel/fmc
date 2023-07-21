<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Livewire\Campaigns\CreateCampaignComponent;

beforeEach(function () {
    EmailList::factory()->create();
    test()->authenticate();
});

it('can create a campaign', function () {
    Livewire::test(CreateCampaignComponent::class)
        ->set('name', 'My campaign')
        ->call('saveCampaign')
        ->assertRedirect(route('mailcoach.campaigns.settings', Campaign::first()));

    $this->assertDatabaseHas(static::getCampaignTableName(), ['name' => 'My campaign']);
});

it('will use default tracking settings', function () {
    Livewire::test(CreateCampaignComponent::class)
        ->set('name', 'my campaign')
        ->call('saveCampaign');

    $this->assertDatabaseHas(static::getCampaignTableName(), [
        'name' => 'my campaign',
        'utm_tags' => 1,
    ]);

    config()->set('mailcoach.campaigns.default_settings.utm_tags', false);

    Livewire::test(CreateCampaignComponent::class)
        ->set('name', 'another campaign')
        ->call('saveCampaign');

    $this->assertDatabaseHas(static::getCampaignTableName(), [
        'name' => 'another campaign',
        'utm_tags' => 0,
    ]);
});

<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CampaignSettingsComponent;

it('can update the settings of a campaign', function () {
    $this->withoutExceptionHandling();

    $this->authenticate();

    $emailList = EmailList::factory()->create();

    $campaign = Campaign::create([
        'name' => 'my campaign',
        'email_list_id' => $emailList->id,
        'show_publicly' => true,
    ]);

    Livewire::test(CampaignSettingsComponent::class, ['campaign' => $campaign])
        ->set('campaign.name', 'updated name')
        ->set('campaign.email_list_id', EmailList::factory()->create()->id)
        ->set('campaign.add_subscriber_tags', true)
        ->set('campaign.add_subscriber_link_tags', true)
        ->set('campaign.utm_tags', true)
        ->set('segment', 'entire_list')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas(self::getCampaignTableName(), [
        'name' => 'updated name',
        'utm_tags' => true,
    ]);
});

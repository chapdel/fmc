<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Livewire\Campaigns\CampaignSettingsComponent;

it('can update the settings of a campaign', function () {
    $this->withoutExceptionHandling();

    $this->authenticate();

    $emailList = EmailList::factory()->create();

    $campaign = Campaign::create([
        'name' => 'my campaign',
        'email_list_id' => $emailList->id,
        'show_publicly' => true,
        'disable_webview' => false,
    ]);

    Livewire::test(CampaignSettingsComponent::class, ['campaign' => $campaign])
        ->set('form.name', 'updated name')
        ->set('form.email_list_id', EmailList::factory()->create()->id)
        ->set('form.add_subscriber_tags', true)
        ->set('form.add_subscriber_link_tags', true)
        ->set('form.disable_webview', true)
        ->set('form.utm_tags', true)
        ->set('segment', 'entire_list')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas(self::getCampaignTableName(), [
        'name' => 'updated name',
        'disable_webview' => true,
    ]);
    $this->assertDatabaseHas(self::getContentItemTableName(), [
        'model_id' => $campaign->id,
        'utm_tags' => true,
    ]);
});

<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Front\Controllers\EmailListCampaignsFeedController;

beforeEach(function () {
    $this->withExceptionHandling();

    $this->emailList = EmailList::factory()->create([
        'campaigns_feed_enabled' => true,
    ]);

    $this->campaign = Campaign::factory()->create([
        'email_list_id' => $this->emailList->id,
        'sent_at' => now(),
        'status' => CampaignStatus::Sent,
    ]);
});

it('can generate a feed', function () {
    $this->withoutExceptionHandling();

    $this
        ->get(action(EmailListCampaignsFeedController::class, $this->emailList->uuid))
        ->assertSee('<?xml', false);
});

it('will not display a feed if it is not enabled', function () {
    $this->emailList->update(['campaigns_feed_enabled' => false]);

    $this
        ->get(action(EmailListCampaignsFeedController::class, $this->emailList->uuid))
        ->assertStatus(404);
});

it('will only contain sent campaigns', function (CampaignStatus $status, bool $shouldBeDisplayed) {
    $assertionMethod = $shouldBeDisplayed
        ? 'assertSee'
        : 'assertDontSee';

    $this->campaign->update([
        'status' => $status,
    ]);

    $this
        ->get(route('mailcoach.feed', $this->emailList->uuid))
        ->assertSuccessful()
        ->$assertionMethod($this->campaign->subject);
})->with([
    [CampaignStatus::Draft, false],
    [CampaignStatus::Sending, true],
    [CampaignStatus::Sent, true],
    [CampaignStatus::Cancelled, false],
]);

it('will not display a campaign that should not be shown publicly', function () {
    $this
        ->get(route('mailcoach.feed', $this->emailList->uuid))
        ->assertSuccessful()
        ->assertSee($this->campaign->subject);

    $this->campaign->update([
        'show_publicly' => false,
    ]);

    $this
        ->get(route('mailcoach.feed', $this->emailList->uuid))
        ->assertSuccessful()
        ->assertDontSee($this->campaign->subject);
});

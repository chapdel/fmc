<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Front\Controllers\EmailListCampaignsFeedController;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    test()->withExceptionHandling();

    test()->emailList = EmailList::factory()->create([
        'campaigns_feed_enabled' => true,
    ]);

    Campaign::factory()->create([
        'email_list_id' => test()->emailList->id,
        'sent_at' => now(),
        'status' => CampaignStatus::SENT,
    ]);
});

it('can generate a feed', function () {
    test()->withoutExceptionHandling();

    $this
        ->get(action(EmailListCampaignsFeedController::class, test()->emailList->uuid))
        ->assertSee('<?xml', false);
});

it('will not display a feed if it is not enabled', function () {
    test()->emailList->update(['campaigns_feed_enabled' => false]);

    $this
        ->get(action(EmailListCampaignsFeedController::class, test()->emailList->uuid))
        ->assertStatus(404);
});

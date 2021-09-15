<?php

use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignTestJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\SendTestEmailController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);
uses(RespondsToApiRequests::class);

beforeEach(function () {
    Bus::fake();

    test()->loginToApi();

    test()->campaign = Campaign::factory()->create([
        'status' => CampaignStatus::DRAFT,
    ]);
});

test('a test email can be sent using the api', function () {
    $campaign = Campaign::factory()->create();

    $this
        ->postJson(action(SendTestEmailController::class, $campaign), ['email' => 'test@example.com'])
        ->assertSuccessful();

    Bus::assertDispatched(function (SendCampaignTestJob $job) {
        expect($job->email)->toEqual('test@example.com');

        return true;
    });
});

test('multiple test emails can be sent using the api', function () {
    $campaign = Campaign::factory()->create();

    $this
        ->postJson(action(SendTestEmailController::class, $campaign), ['email' => 'test@example.com,test2@example.com,test3@example.com'])
        ->assertSuccessful();

    Bus::assertDispatchedTimes(SendCampaignTestJob::class, 3);
});

it('will not send a test mail for a campaign that has already been sent', function () {
    test()->withExceptionHandling();

    test()->campaign->update(['status' => CampaignStatus::SENT]);

    $this
        ->postJson(action(SendTestEmailController::class, test()->campaign), ['email' => 'test@example.com'])
        ->assertJsonValidationErrors('campaign');

    Bus::assertNotDispatched(SendCampaignJob::class);
});

<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\Campaigns;

use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Enums\CampaignStatus;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\SendTestEmailController;
use Spatie\Mailcoach\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Jobs\SendTestMailJob;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class SendTestEmailControllerTest extends TestCase
{
    use RespondsToApiRequests;

    private Campaign $campaign;

    public function setUp(): void
    {
        parent::setUp();

        Bus::fake();

        $this->loginToApi();

        $this->campaign = factory(Campaign::class)->create([
            'status' => CampaignStatus::DRAFT,
        ]);
    }

    /** @test */
    public function a_test_email_can_be_sent_using_the_api()
    {
        $campaign = factory(Campaign::class)->create();

        $this
            ->postJson(action(SendTestEmailController::class, $campaign), ['email' => 'test@example.com'])
            ->assertSuccessful();

        Bus::assertDispatched(function (SendTestMailJob $job) {
            $this->assertEquals('test@example.com', $job->email);

            return true;
        });
    }

    /** @test */
    public function multiple_test_emails_can_be_sent_using_the_api()
    {
        $campaign = factory(Campaign::class)->create();

        $this
            ->postJson(action(SendTestEmailController::class, $campaign), ['email' => 'test@example.com,test2@example.com,test3@example.com'])
            ->assertSuccessful();

        Bus::assertDispatchedTimes(SendTestMailJob::class, 3);
    }

    /** @test */
    public function it_will_not_send_a_test_mail_for_a_campaign_that_has_already_been_sent()
    {
        $this->withExceptionHandling();

        $this->campaign->update(['status' => CampaignStatus::SENT]);

        $this
            ->postJson(action(SendTestEmailController::class, $this->campaign), ['email' => 'test@example.com'])
            ->assertJsonValidationErrors('campaign');

        Bus::assertNotDispatched(SendCampaignJob::class);
    }
}

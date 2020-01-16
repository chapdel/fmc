<?php

namespace Spatie\Mailcoach\Tests\Feature\Controllers\App\Campaigns;

use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Enums\CampaignStatus;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\SendTestEmailController;
use Spatie\Mailcoach\Jobs\SendTestMailJob;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;

class SendTestEmailControllerTest extends TestCase
{
    /** @var \Spatie\Mailcoach\Models\Campaign */
    private $campaign;

    public function setUp(): void
    {
        parent::setUp();

        $this->authenticate();

        $this->campaign = factory(Campaign::class)->create([
            'status' => CampaignStatus::DRAFT,
        ]);

        Bus::fake();
    }

    /** @test */
    public function it_can_send_test_mails()
    {
        $this->post(
            action(SendTestEmailController::class, $this->campaign),
            ['emails' => 'test@example.com']
        );

        Bus::assertDispatched(SendTestMailJob::class);
    }
}

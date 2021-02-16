<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\App\Campaigns;

use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendTestMailJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\SendTestEmailController;
use Spatie\Mailcoach\Tests\TestCase;

class SendTestEmailControllerTest extends TestCase
{
    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign */
    protected $campaign;

    public function setUp(): void
    {
        parent::setUp();

        $this->authenticate();

        $this->campaign = Campaign::factory()->create([
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

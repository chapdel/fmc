<?php

namespace Spatie\Mailcoach\Tests\Commands;

use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Commands\CalculateStatisticsCommand;
use Spatie\Mailcoach\Commands\CleanupProcessedFeedbackCommand;
use Spatie\Mailcoach\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;
use Spatie\WebhookClient\Models\WebhookCall;

class CleanupProcessedFeedbackCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow();
    }

    /** @test */
    public function it_deletes_only_processed_webhooks_older_than_the_default_1_hour_interval()
    {
        $keep = WebhookCall::create(['name' => 'ses-feedback', 'processed_at' => now()]);
        $delete = WebhookCall::create(['name' => 'ses-feedback', 'processed_at' => now()->subHours(2)]);

        $this->artisan(CleanupProcessedFeedbackCommand::class)->assertExitCode(0);

        $this->assertNotNull($keep->refresh());
        $this->assertDeleted($delete);
    }

    /** @test */
    public function it_can_be_passed_the_hours_interval_in_the_command_options()
    {
        $keep1 = WebhookCall::create(['name' => 'ses-feedback', 'processed_at' => now()]);
        $keep2 = WebhookCall::create(['name' => 'ses-feedback', 'processed_at' => now()->subHours(2)]);
        $delete = WebhookCall::create(['name' => 'ses-feedback', 'processed_at' => now()->subHours(6)]);

        $this->artisan(CleanupProcessedFeedbackCommand::class, ['--hours' => 5])->assertExitCode(0);

        $this->assertNotNull($keep1->refresh());
        $this->assertNotNull($keep2->refresh());
        $this->assertDeleted($delete);
    }

    /** @test */
    public function it_only_deletes_calls_that_are_from_the_feedback_packages()
    {
        $delete1 = WebhookCall::create(['name' => 'ses-feedback', 'processed_at' => now()->subHours(6)]);
        $delete2 = WebhookCall::create(['name' => 'sendgrid-feedback', 'processed_at' => now()->subHours(6)]);
        $delete3 = WebhookCall::create(['name' => 'mailgun-feedback', 'processed_at' => now()->subHours(6)]);
        $delete4 = WebhookCall::create(['name' => 'postmark-feedback', 'processed_at' => now()->subHours(6)]);
        $keep = WebhookCall::create(['name' => 'stripe', 'processed_at' => now()->subHours(6)]);

        $this->artisan(CleanupProcessedFeedbackCommand::class, ['--hours' => 5])->assertExitCode(0);

        $this->assertNotNull($keep->refresh());
        $this->assertDeleted($delete1);
        $this->assertDeleted($delete2);
        $this->assertDeleted($delete3);
        $this->assertDeleted($delete4);
    }
}

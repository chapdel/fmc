<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Commands;

use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;
use Spatie\Mailcoach\Domain\Shared\Commands\RetryPendingSendsCommand;
use Spatie\Mailcoach\Tests\TestCase;

class RetryPendingSendsCommandTest extends TestCase
{
    /** @test */
    public function it_will_dispatch_a_job_for_each_pending_Send()
    {
        Queue::fake();

        $pendingSend = SendFactory::new()->create([
            'sent_at' => null,
        ]);

        $sentSend = SendFactory::new()->create([
            'sent_at' => now(),
        ]);

        $this->artisan(RetryPendingSendsCommand::class)->assertExitCode(0);

        Queue::assertPushed(SendCampaignMailJob::class, 1);
        Queue::assertPushed(SendCampaignMailJob::class, fn (SendCampaignMailJob $job) => $job->pendingSend->id === $pendingSend->id);
    }
}

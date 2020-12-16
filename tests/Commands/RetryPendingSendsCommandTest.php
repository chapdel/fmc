<?php

namespace Spatie\Mailcoach\Tests\Commands;

use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Campaign\Commands\RetryPendingSendsCommand;
use Spatie\Mailcoach\Database\Factories\CampaignSendFactory;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendMailJob;
use Spatie\Mailcoach\Tests\TestCase;

class RetryPendingSendsCommandTest extends TestCase
{
    /** @test */
    public function it_will_dispatch_a_job_for_each_pending_Send()
    {
        Queue::fake();

        $pendingSend = CampaignSendFactory::new()->create([
            'sent_at' => null,
        ]);

        $sentSend = CampaignSendFactory::new()->create([
            'sent_at' => now(),
        ]);

        $this->artisan(RetryPendingSendsCommand::class)->assertExitCode(0);

        Queue::assertPushed(SendMailJob::class, 1);
        Queue::assertPushed(SendMailJob::class, fn (SendMailJob $job) => $job->pendingSend->id === $pendingSend->id);
    }
}

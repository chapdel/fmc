<?php

namespace Spatie\Mailcoach\Tests\Commands;

use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Commands\RetryPendingSendsCommand;
use Spatie\Mailcoach\Jobs\SendMailJob;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;

class RetryPendingSendsCommandTest extends TestCase
{
    /** @test */
    public function it_will_dispatch_a_job_for_each_pending_Send()
    {
        Queue::fake();

        $pendingSend = factory(Send::class)->create([
            'sent_at' => null,
        ]);

        $sentSend = factory(Send::class)->create([
            'sent_at' => now(),
        ]);

        $this->artisan(RetryPendingSendsCommand::class)->assertExitCode(0);

        Queue::assertPushed(SendMailJob::class, 1);
        Queue::assertPushed(SendMailJob::class, fn (SendMailJob $job) => $job->pendingSend->id === $pendingSend->id);
    }
}

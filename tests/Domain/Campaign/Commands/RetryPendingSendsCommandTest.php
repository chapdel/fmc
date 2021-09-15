<?php

use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignMailJob;
use Spatie\Mailcoach\Domain\Shared\Commands\RetryPendingSendsCommand;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('will dispatch a job for each pending send', function () {
    Queue::fake();

    $pendingSend = SendFactory::new()->create([
        'sent_at' => null,
    ]);

    $sentSend = SendFactory::new()->create([
        'sent_at' => now(),
    ]);

    test()->artisan(RetryPendingSendsCommand::class)->assertExitCode(0);

    Queue::assertPushed(SendCampaignMailJob::class, 1);
    Queue::assertPushed(SendCampaignMailJob::class, fn (SendCampaignMailJob $job) => $job->pendingSend->id === $pendingSend->id);
});

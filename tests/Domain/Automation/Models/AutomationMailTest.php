<?php

use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;

it('can dispatch a calculate statistics job', function () {
    Queue::fake();
    $automationMail = AutomationMail::factory()->create();

    $automationMail->dispatchCalculateStatistics();

    Queue::assertPushed(CalculateStatisticsJob::class);
});

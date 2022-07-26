<?php

use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Shared\Support\CalculateStatisticsLock;

beforeEach(function () {
    Queue::fake();
});

it('can dispatch a calculate statistics job', function () {
    $automationMail = AutomationMail::factory()->create();

    $automationMail->dispatchCalculateStatistics();

    Queue::assertPushed(CalculateStatisticsJob::class);
});

it('wont dispatch a calculate statistics job if it doesnt have any new sends', function () {
    $automationMail = AutomationMail::factory()->create([
        'statistics_calculated_at' => now(),
    ]);

    $automationMail->dispatchCalculateStatistics();

    Queue::assertNotPushed(CalculateStatisticsJob::class);

    \Spatie\Mailcoach\Domain\Shared\Models\Send::factory()->create([
        'automation_mail_id' => $automationMail->id,
    ]);

    $automationMail->dispatchCalculateStatistics();

    Queue::assertPushed(CalculateStatisticsJob::class);
});

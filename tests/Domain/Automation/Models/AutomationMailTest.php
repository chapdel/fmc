<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;

it('can dispatch a calculate statistics job', function () {
    Queue::fake();
    $automationMail = AutomationMail::factory()->create();

    $automationMail->dispatchCalculateStatistics();

    Queue::assertPushed(CalculateStatisticsJob::class);
});

it('wont calculate statistics if it doesnt have any new sends', function () {
    $automationMail = AutomationMail::factory()->create([
        'statistics_calculated_at' => now(),
    ]);

    $queryCount = 0;
    DB::listen(function ($query) use (&$queryCount) {
        $queryCount++;
    });

    expect($queryCount)->toBe(0);

    $automationMail->dispatchCalculateStatistics();

    expect($queryCount)->toBe(6); // 5 queries to get events + 1 to update statistics calculated at

    \Spatie\Mailcoach\Domain\Shared\Models\Send::factory()->create([
        'automation_mail_id' => $automationMail->id,
    ]);

    $automationMail->dispatchCalculateStatistics();

    expect($queryCount)->toBeGreaterThan(20); // A lot of queries to calculate the statistics
});

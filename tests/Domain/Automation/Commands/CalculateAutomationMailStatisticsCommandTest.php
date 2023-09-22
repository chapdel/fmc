<?php

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Commands\CalculateAutomationMailStatisticsCommand;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Domain\Content\Jobs\CalculateStatisticsJob;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    TestTime::freeze('Y-m-d H:i:s', '2019-01-01 00:00:00');
});

it('will recalculate statistics of active automation mails', function () {
    Queue::fake();

    $automationMail = AutomationMail::factory()->create();
    AutomationMail::factory()->create();

    Automation::create()
        ->runEvery(CarbonInterval::minute())
        ->to(EmailList::factory()->create())
        ->triggerOn(new SubscribedTrigger)
        ->chain([
            new SendAutomationMailAction($automationMail),
        ])->start();

    test()->artisan(CalculateAutomationMailStatisticsCommand::class)
        ->assertExitCode(0);

    $this->processQueuedJobs();

    Queue::assertPushed(CalculateStatisticsJob::class, 1);
});

it('can recalculate the statistics of a single automation mail', function () {
    $automationMail = AutomationMail::factory()->create();

    test()->artisan(CalculateAutomationMailStatisticsCommand::class, ['automationMailId' => $automationMail->id])
        ->assertExitCode(0);

    test()->assertNotNull($automationMail->refresh()->contentItem->statistics_calculated_at);
});

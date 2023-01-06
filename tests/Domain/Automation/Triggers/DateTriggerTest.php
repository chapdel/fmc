<?php

use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Commands\RunAutomationTriggersCommand;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunAutomationForSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\DateTrigger;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    test()->automationMail = AutomationMail::factory()->create(['subject' => 'Welcome']);

    test()->emailList = EmailList::factory()->create();
});

it('triggers on a specific date', function () {
    Queue::fake();

    TestTime::setTestNow(Carbon::create(2020, 01, 01));

    $trigger = new DateTrigger(Carbon::create(2020, 01, 02));

    $automation = Automation::create()
        ->name('New year!')
        ->runEvery(CarbonInterval::minute())
        ->to(test()->emailList)
        ->triggerOn($trigger)
        ->chain([
            new SendAutomationMailAction(test()->automationMail),
        ])
        ->start();

    test()->emailList->subscribe('john@doe.com');

    Artisan::call(RunAutomationTriggersCommand::class);

    expect($automation->actions->first()->subscribers)->toBeEmpty();

    TestTime::addDay()->addSeconds(2);

    Artisan::call(RunAutomationTriggersCommand::class);
    $this->processQueuedJobs();

    Queue::assertPushed(RunAutomationForSubscriberJob::class, 1);
    Queue::assertPushed(
        RunAutomationForSubscriberJob::class,
        function (RunAutomationForSubscriberJob $job) use ($automation) {
            expect($job->subscriber->email)->toBe('john@doe.com');
            expect($job->automation->id)->toBe($automation->id);

            $job->handle();

            return true;
        }
    );

    Artisan::call(RunAutomationTriggersCommand::class);

    $this->processQueuedJobs();

    // It triggers only once
    Queue::assertPushed(RunAutomationForSubscriberJob::class, 1);
});

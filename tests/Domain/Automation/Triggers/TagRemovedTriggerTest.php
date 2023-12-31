<?php

use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunAutomationForSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\TagRemovedTrigger;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    test()->automationMail = AutomationMail::factory()->create();

    test()->emailList = EmailList::factory()->create();
});

it('triggers when a tag is removed from a subscriber', function () {
    Queue::fake();

    TestTime::setTestNow(Carbon::create(2020, 01, 01));

    $trigger = new TagRemovedTrigger('opened');

    $automation = Automation::create()
        ->name('New year!')
        ->runEvery(CarbonInterval::minute())
        ->to(test()->emailList)
        ->triggerOn($trigger)
        ->chain([
            new SendAutomationMailAction(test()->automationMail),
        ])
        ->start();

    test()->refreshServiceProvider();

    test()->emailList->subscribe('john@doe.com');

    Subscriber::first()->addTag('opened');

    expect($automation->actions->first()->fresh()->subscribers)->toBeEmpty();

    Subscriber::first()->removeTag('opened');

    Queue::assertPushed(
        RunAutomationForSubscriberJob::class,
        function (RunAutomationForSubscriberJob $job) {
            expect($job->subscriber->email)->toBe('john@doe.com');

            return true;
        }
    );
});

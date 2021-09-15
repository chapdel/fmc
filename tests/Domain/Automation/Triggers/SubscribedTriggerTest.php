<?php

use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunAutomationForSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

uses(TestCase::class);

beforeEach(function () {
    test()->automationMail = AutomationMail::factory()->create(['subject' => 'Welcome']);

    test()->emailList = EmailList::factory()->create();
});

it('triggers when a subscriber is subscribed', function () {
    Queue::fake();

    TestTime::setTestNow(Carbon::create(2020, 01, 01));

    $trigger = new SubscribedTrigger();

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

    expect($automation->actions->first()->fresh()->subscribers)->toBeEmpty();

    test()->emailList->subscribeSkippingConfirmation('john@doe.com');

    Queue::assertPushed(
        RunAutomationForSubscriberJob::class,
        function (RunAutomationForSubscriberJob $job) use ($automation) {
            expect($job->subscriber->email)->toBe('john@doe.com');
            expect($job->automation->id)->toBe($automation->id);

            return true;
        }
    );
});

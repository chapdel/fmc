<?php

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunAutomationForSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomShouldAutomationRunForSubscriberAction;



beforeEach(function () {
    test()->emailList = EmailList::factory()->create();
    test()->automationMail = AutomationMail::factory()->create();

    Queue::fake();
});

it('runs the automation for a subscriber', function () {
    /** @var Automation $automation */
    $automation = Automation::create()
        ->name('New year!')
        ->runEvery(CarbonInterval::minute())
        ->to(test()->emailList)
        ->triggerOn(new SubscribedTrigger())
        ->chain([
            new SendAutomationMailAction(test()->automationMail),
        ])
        ->start();

    test()->refreshServiceProvider();

    expect($automation->actions->first()->fresh()->subscribers)->toBeEmpty();

    expect($automation->actions()->first()->subscribers->count())->toEqual(0);

    $jane = test()->emailList->subscribeSkippingConfirmation('jane@doe.com');

    (new RunAutomationForSubscriberJob($automation, $jane))->handle();

    expect($automation->actions()->first()->subscribers->count())->toEqual(1);
});

it('does nothing when the automation isnt started', function () {
    /** @var Automation $automation */
    $automation = Automation::create()
        ->name('New year!')
        ->runEvery(CarbonInterval::minute())
        ->to(test()->emailList)
        ->triggerOn(new SubscribedTrigger())
        ->chain([
            new SendAutomationMailAction(test()->automationMail),
        ]);

    test()->refreshServiceProvider();

    expect($automation->actions->first()->fresh()->subscribers)->toBeEmpty();

    $jane = test()->emailList->subscribeSkippingConfirmation('jane@doe.com');

    expect($automation->actions()->first()->subscribers->count())->toEqual(0);

    (new RunAutomationForSubscriberJob($automation, $jane))->handle();

    expect($automation->actions()->first()->subscribers->count())->toEqual(0);
});

it('uses the configured action', function () {
    config()->set('mailcoach.automation.actions.should_run_for_subscriber', CustomShouldAutomationRunForSubscriberAction::class);

    /** @var Automation $automation */
    $automation = Automation::create()
        ->name('New year!')
        ->runEvery(CarbonInterval::minute())
        ->to(test()->emailList)
        ->triggerOn(new SubscribedTrigger())
        ->chain([
            new SendAutomationMailAction(test()->automationMail),
        ])
        ->start();

    test()->refreshServiceProvider();

    expect($automation->actions->first()->fresh()->subscribers)->toBeEmpty();

    $jane = test()->emailList->subscribeSkippingConfirmation('jane@doe.com');

    expect($automation->actions()->first()->subscribers->count())->toEqual(0);

    test()->expectExceptionMessage("CustomShouldAutomationRunForSubscriberAction was used");

    (new RunAutomationForSubscriberJob($automation, $jane))->handle();
});

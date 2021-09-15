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

uses(TestCase::class);

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

    test()->assertEmpty($automation->actions->first()->fresh()->subscribers);

    test()->assertEquals(0, $automation->actions()->first()->subscribers->count());

    $jane = test()->emailList->subscribeSkippingConfirmation('jane@doe.com');

    (new RunAutomationForSubscriberJob($automation, $jane))->handle();

    test()->assertEquals(1, $automation->actions()->first()->subscribers->count());
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

    test()->assertEmpty($automation->actions->first()->fresh()->subscribers);

    $jane = test()->emailList->subscribeSkippingConfirmation('jane@doe.com');

    test()->assertEquals(0, $automation->actions()->first()->subscribers->count());

    (new RunAutomationForSubscriberJob($automation, $jane))->handle();

    test()->assertEquals(0, $automation->actions()->first()->subscribers->count());
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

    test()->assertEmpty($automation->actions->first()->fresh()->subscribers);

    $jane = test()->emailList->subscribeSkippingConfirmation('jane@doe.com');

    test()->assertEquals(0, $automation->actions()->first()->subscribers->count());

    test()->expectExceptionMessage("CustomShouldAutomationRunForSubscriberAction was used");

    (new RunAutomationForSubscriberJob($automation, $jane))->handle();
});

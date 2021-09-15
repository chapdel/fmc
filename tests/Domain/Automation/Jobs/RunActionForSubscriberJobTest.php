<?php

use Carbon\CarbonInterval;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunActionForSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomSendAutomationMailAction;
use Spatie\TestTime\TestTime;

uses(TestCase::class);

beforeEach(function () {
    test()->emailList = EmailList::factory()->create();
});

it('runs the action for a subscriber', function () {
    TestTime::freeze();

    $automation = Automation::factory()->create();

    $action = Action::create([
        'automation_id' => $automation->id,
        'action' => new WaitAction(CarbonInterval::day()),
        'order' => 1,
    ]);

    $action2 = Action::create([
        'automation_id' => $automation->id,
        'action' => new WaitAction(CarbonInterval::minute()),
        'order' => 2,
    ]);

    $subscriber = test()->emailList->subscribe('john@doe.com');

    $action->subscribers()->attach($subscriber);

    dispatch_sync(new RunActionForSubscriberJob($action, $subscriber));

    test()->assertEquals($action->id, $subscriber->actions->first()->id);

    TestTime::addDays(2);

    dispatch_sync(new RunActionForSubscriberJob($action, $subscriber));

    test()->assertEquals(2, $subscriber->actions()->count());

    dispatch_sync(new RunActionForSubscriberJob($action, $subscriber));

    // it won't add it twice
    test()->assertEquals(2, $subscriber->actions()->count());
});

it('optionally passes the action subscriber', function () {
    TestTime::freeze();

    $automation = Automation::factory()->create();
    $automationMail = AutomationMail::factory()->create();

    $action = Action::create([
        'automation_id' => $automation->id,
        'action' => new CustomSendAutomationMailAction($automationMail),
        'order' => 1,
    ]);

    $subscriber = test()->emailList->subscribe('john@doe.com');

    $action->subscribers()->attach($subscriber);

    test()->expectExceptionMessage("ActionSubscriber is set!");

    dispatch_sync(new RunActionForSubscriberJob($action, $subscriber));
});

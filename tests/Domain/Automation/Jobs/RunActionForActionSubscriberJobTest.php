<?php

use Carbon\CarbonInterval;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Jobs\RunActionForActionSubscriberJob;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    test()->emailList = EmailList::factory()->create();
});

it('runs the action for an action subscriber', function () {
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

    $actionSubscriber = ActionSubscriber::first();
    $actionSubscriber->update(['job_dispatched_at' => now()]);

    dispatch_sync(new RunActionForActionSubscriberJob($actionSubscriber));

    expect($subscriber->actions->first()->id)->toEqual($action->id);

    TestTime::addDays(2);

    $actionSubscriber->update(['job_dispatched_at' => now()]);
    dispatch_sync(new RunActionForActionSubscriberJob($actionSubscriber));

    expect($subscriber->actions()->count())->toEqual(2);

    $actionSubscriber->update(['job_dispatched_at' => now()]);
    dispatch_sync(new RunActionForActionSubscriberJob($actionSubscriber));

    // it won't add it twice
    expect($subscriber->actions()->count())->toEqual(2);
});

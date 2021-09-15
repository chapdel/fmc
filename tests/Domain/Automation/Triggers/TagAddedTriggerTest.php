<?php

use Carbon\CarbonInterval;
use Illuminate\Support\Carbon;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\TagAddedTrigger;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

uses(TestCase::class);

beforeEach(function () {
    test()->automationMail = AutomationMail::factory()->create(['subject' => 'Welcome']);

    test()->emailList = EmailList::factory()->create();
});

it('triggers when a subscriber gets a tag', function () {
    TestTime::setTestNow(Carbon::create(2020, 01, 01));

    $trigger = new TagAddedTrigger('opened');

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

    test()->assertEmpty($automation->actions->first()->fresh()->subscribers);

    Subscriber::first()->addTag('clicked');

    test()->assertEmpty($automation->actions->first()->fresh()->subscribers);

    Subscriber::first()->addTag('opened');

    test()->assertEquals(1, $automation->actions()->first()->subscribers->count());
});

it('triggers when a new subscriber has a tag', function () {
    TestTime::setTestNow(Carbon::create(2020, 01, 01));

    $trigger = new TagAddedTrigger('opened');

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

    test()->assertEmpty($automation->actions->first()->fresh()->subscribers);

    $pendingSubscriber = Subscriber::createWithEmail('john@doe.com')
        ->tags(['opened'])
        ->subscribeTo(test()->emailList);

    test()->assertEquals(1, $automation->actions()->first()->subscribers->count());
});

it('triggers when a new confirmed subscriber has a tag', function () {
    TestTime::setTestNow(Carbon::create(2020, 01, 01));

    test()->emailList->update([
        'requires_confirmation' => true,
    ]);

    $trigger = new TagAddedTrigger('opened');

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

    test()->assertEmpty($automation->actions->first()->fresh()->subscribers);

    $subscriber = Subscriber::createWithEmail('john@doe.com')
        ->tags(['opened'])
        ->subscribeTo(test()->emailList);

    test()->assertEmpty($automation->actions->first()->fresh()->subscribers);

    $subscriber->confirm();

    test()->assertEquals(1, $automation->actions()->first()->subscribers->count());
});
